<?php namespace Src;

use Src\Models\Coach;
use Src\Models\Student;
use Src\Models\CoachStudent;

/**
 * Class Factory
 *
 * @package Src
 */
class Factory
{
    /**
     * @param $table
     * @param $count
     * @return bool
     */
    public static function create($table, $count)
    {
        if(is_null(self::getInstanceOfModel($table))){
           return false;
        }

        $model = self::getInstanceOfModel($table);
        for ($i = 0; $i < $count; $i++) {
            $model->name = "test_name";
            $model->save();
        }
    }

    /**
     * @param $table
     * @return \Src\Models\Coach|\Src\Models\CoachStudent|\Src\Models\Student
     */
    private static function getInstanceOfModel($table)
    {
        switch ($table) {
            case "students":
                return new Student();
                break;
            case "coaches":
                return new Coach();
                break;
            default:
                return null;
        }
    }
}