<?php namespace Src;

use PDO;

/**
 * Class DB
 */
class DB
{
    const HOST ="localhost";
    const DB_NAME ="coding_task";
    const DB_USER ="root";
    const DB_PASSWORD ="";

    /** @var $db */
    public static $db;
    /** @var $connection */
    public $connection;

    /**
     * DB constructor.
     */
    private function __construct(){}


    /**
     * @return \Src\DB
     */
    public static function getInstance(){
        if(self::$db === null){
            self::$db = new DB();
        }
        self::$db->connection = self::$db->connect();
        return self::$db;
    }

    /**
     * @return \PDO
     */
    public function connect(){
        return new PDO("mysql:host=".self::HOST.";dbname=".self::DB_NAME, self::DB_USER, self::DB_PASSWORD);
    }

    /**
     * @param $table
     */
    public static function truncate($table)
    {
        self::$db->connection->query("SET foreign_key_checks = 0;");
        self::$db->connection->exec("TRUNCATE TABLE `$table`");
        self::$db->connection->query("SET foreign_key_checks = 1;");
    }

    /**
     * @param $table
     * @param $data
     * @return mixed
     */
    public function insert($table,$data){

        $attributes = implode(",", array_keys($data));
        foreach ($data as $key => $value){
            $values []= ":".$key;
        }
        $values = implode(", ", $values);

        $statement = $this->connection->prepare("INSERT INTO $table ($attributes) VALUES ($values)");

        for($i=0; $i<count($data); $i++){
            $statement->bindParam(':'.array_keys($data)[$i], array_values($data)[$i]);
        }
        $statement->execute();
        return $this;
    }
}