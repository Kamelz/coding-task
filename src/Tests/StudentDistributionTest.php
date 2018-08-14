<?php

/**
 * Class StudentDistributionTest
 */

use Src\DB;
use Src\Factory;
use Src\Models\Coach;
use Src\Models\Student;
use Src\Models\CoachStudent;
use PHPUnit\Framework\TestCase;

class StudentDistributionTest extends TestCase
{
    /** @var $db */
    public $db;

    public function setUp()
    {
        parent::setUp();
        $this->db = DB::getInstance();
        CoachStudent::emptyTable();
        Coach::emptyTable();
        Student::emptyTable();
    }

    /** @test */
    function it_can_assign_student_to_a_coach()
    {
        // we have a student
        $student = new Student();
        $student->name = "test_student";
        $student = $student->save();
        $student->id = $student->getLastInsertedId();

        // we have a coach
        $coach = new Coach();
        $coach->name = "test_coach";
        $coach->save();
        $coach->id = $coach->getLastInsertedId();

        $coach->add($student->id);

        $result = $this->db ->connection
            ->query("select * from `coaches_students` where `student_id` = $student->id AND `coach_id` = $coach->id");
        $assignmentList = $result->fetchAll();

        $this->assertEquals($coach->id,$assignmentList[0]['coach_id']);
        $this->assertEquals($student->id,$assignmentList[0]['student_id']);
    }


    /** @test */
    public function it_can_assign_students_evenly(){

        Factory::create('students',20);
        Factory::create('coaches',3);
        (new Coach)->distributeStudentsEvenly();

        $result = $this->db ->connection
            ->query("select count(*) from `coaches_students` where `coach_id` = 1");
        $firstCoachStudentCount =$result->fetchColumn();

        $result = $this->db ->connection
            ->query("select count(*) from `coaches_students` where `coach_id` = 2");
        $secondCoachStudentCount =$result->fetchColumn();

        $result = $this->db ->connection
            ->query("select count(*) from `coaches_students` where `coach_id` = 3");
        $thirdCoachStudentCount =$result->fetchColumn();

        $this->assertEquals(7,$firstCoachStudentCount);
        $this->assertEquals(7,$secondCoachStudentCount);
        $this->assertEquals(6,$thirdCoachStudentCount);
    }



    /** @test */
    public function it_can_assign_students_evenly_based_on_capacity()
    {
        Factory::create('students',100);
        $coachWith20Capacity =new Coach();
        $coachWith20Capacity->name = "test_coach";
        $coachWith20Capacity->capacity = "20";
        $coachWith20Capacity->save();
        $coachWith20Capacity->id = $coachWith20Capacity->getLastInsertedId();

        $coachWith30Capacity = new Coach();
        $coachWith30Capacity->name = "test_coach";
        $coachWith30Capacity->capacity = "30";
        $coachWith30Capacity->save();
        $coachWith30Capacity->id = $coachWith30Capacity->getLastInsertedId();

        $coachWith50Capacity = new Coach();
        $coachWith50Capacity->name = "test_coach";
        $coachWith50Capacity->capacity = "50";
        $coachWith50Capacity->save();
        $coachWith50Capacity->id = $coachWith50Capacity->getLastInsertedId();

        (new Coach)->distributeStudentsEvenly(true);

        $result = $this->db ->connection
            ->query("select count(*) from `coaches_students` where `coach_id` = 1");
        $firstCoachStudentCount =$result->fetchColumn();

        $result = $this->db ->connection
            ->query("select count(*) from `coaches_students` where `coach_id` = 2");
        $secondCoachStudentCount =$result->fetchColumn();

        $result = $this->db ->connection
            ->query("select count(*) from `coaches_students` where `coach_id` = 3");
        $thirdCoachStudentCount =$result->fetchColumn();

        $this->assertEquals(20,$firstCoachStudentCount);
        $this->assertEquals(30,$secondCoachStudentCount);
        $this->assertEquals(50,$thirdCoachStudentCount);

    }

    /** @test */
    public function it_can_assign_students_evenly_even_if_the_coach_has_students()
    {
        Factory::create('students',20);
        Factory::create('coaches',3);
        (new Coach)->distributeStudentsEvenly();
        Factory::create('students',10);
        (new Coach)->distributeStudentsEvenly();


        $result = $this->db ->connection
            ->query("select count(*) from `coaches_students` where `coach_id` = 1");
        $firstCoachStudentCount =$result->fetchColumn();

        $result = $this->db ->connection
            ->query("select count(*) from `coaches_students` where `coach_id` = 2");
        $secondCoachStudentCount =$result->fetchColumn();

        $result = $this->db ->connection
            ->query("select count(*) from `coaches_students` where `coach_id` = 3");
        $thirdCoachStudentCount =$result->fetchColumn();

        $this->assertEquals(10,$firstCoachStudentCount);
        $this->assertEquals(10,$secondCoachStudentCount);
        $this->assertEquals(10,$thirdCoachStudentCount);

    }
}