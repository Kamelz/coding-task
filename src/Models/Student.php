<?php namespace Src\Models;

use Src\DB;

/**
 * Class Student
 */
class Student
{
    const TABLE = "students";

    /** @var $id */
    public $id;

    /** @var $name */
    public $name;

    /** @var $db */
    public $db;

    public function __construct()
    {
        $this->db = DB::getInstance();
    }

    /**
     * @return $this
     */
    public function save(){
        $this->db->insert('students',['name' => $this->name]);
        $this->id = $this->getLastInsertedId();

        return $this;
    }

    /**
     * @return mixed
     */
    public function getLastInsertedId(){
        return $this->db->connection->lastInsertId();
    }
    /**
     *
     */
    public static function emptyTable(){
        DB::truncate(self::TABLE);
    }
}