<?php namespace Src\Models;

use Src\DB;

/**
 * Class BaseModel
 *
 * @package Src\Models
 */
class BaseModel
{

    /** @var $db */
    public $db;

    public function __construct()
    {
        $this->db = DB::getInstance();
    }

    /**
     * @param $table
     */
    public static function emptyTable($table)
    {
        DB::truncate($table);
    }

    /**
     * @return mixed
     */
    public function getLastInsertedId()
    {
        return $this->db->connection->lastInsertId();
    }


}