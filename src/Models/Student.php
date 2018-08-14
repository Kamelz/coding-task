<?php namespace Src\Models;

use Src\DB;

/**
 * Class Student
 */
class Student extends BaseModel
{
    const TABLE = "students";

    /** @var $id */
    public $id;

    /** @var $name */
    public $name;

    /**
     * @return $this
     */
    public function save(){
        $this->db->insert(self::TABLE,['name' => $this->name]);
        $this->id = $this->getLastInsertedId();

        return $this;
    }
}