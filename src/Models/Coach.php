<?php namespace Src\Models;

use Src\DB;

/**
 * Class Coach
 *
 * @property \Src\DB db
 */
class Coach
{
    const TABLE = "coaches";

    /** @var $id */
    public $id;

    /** @var $name */
    public $name;

    /** @var $capacity */
    public $capacity;

    /** @var $db */
    public $db;

    public function __construct()
    {
        $this->db = DB::getInstance();
    }

    /**
     * @return $this
     */
    public function save()
    {
        $this->db->insert(self::TABLE, [
            'name'     => $this->name,
            'capacity' => $this->capacity,
        ]);
        $this->id = $this->getLastInsertedId();

        return $this;
    }


    /**
     * @return mixed
     */
    public function getLastInsertedId()
    {
        return $this->db->connection->lastInsertId();
    }

    /**
     *
     */
    public static function emptyTable()
    {
        DB::truncate(self::TABLE);
    }

    /**
     * @param $studentId
     * @param null $coachId
     */
    public function add($studentId, $coachId = null)
    {
        $coachId = is_null($coachId)? $this->id : $coachId;
        $this->db->insert('coaches_students', [
            'coach_id'   => $coachId,
            'student_id' => $studentId,
        ]);
    }


    /**
     * @param bool $withCapacity
     */
    public function distributeStudentsEvenly($withCapacity = false)
    {
        $result = $this->db->connection->query('select * from `coaches` where 1');
        $coaches = $result->fetchAll();


        $result = $this->db->connection->query('select * from `students` where 1');
        $students = $result->fetchAll();

        $share = $withCapacity === true? $this->getShareBasedOnCapacity($coaches, $students) : $this->getShare($coaches, $students);

        // assign
        for ($i = 0; $i < count($share); $i++) {
            $students = array_values($students);
            $studentsCount = count($students);
            for ($j = 0; $j < $studentsCount; $j++) {
                if ($share[$i]['shareCount'] == 0) {
                    break;
                }
                $this->add($students[$j]['id'], $share[$i]['coach_id']);
                $share[$i]['shareCount']--;
                unset($students[$j]);
            }
        }
    }


    /**
     * @param $coaches
     * @param $students
     * @return array
     */
    private function getShare($coaches, $students)
    {

        // check if coach already has students
        //todo

        $coachesCount = count($coaches);
        $share = [];
        // get the share of each coach
        for ($i = 0; $i < $coachesCount; $i++) {
            $share[$i]['coach_id'] = $coaches[$i]['id'];
            $share[$i]['shareCount'] = floor((count($students) + $coachesCount - ($i + 1)) / $coachesCount);
        }

        return $share;
    }

    /**
     * @param $coaches
     * @param $students
     * @return array
     */
    private function getShareBasedOnCapacity($coaches, $students)
    {
        $coachesCount = count($coaches);
        $share = [];
        // get the share of each coach
        for ($i = 0; $i < $coachesCount; $i++) {
            $share[$i]['coach_id'] = $coaches[$i]['id'];
            $share[$i]['shareCount'] = $coaches[$i]['capacity'] * count($students) / 100;
        }

        return $share;
    }


}