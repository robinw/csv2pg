<?php


namespace Commands;


class CreateTable extends Command
{
    private $db;

    public function __construct(\PDO $db)
    {
        $this->db = $db;
    }

    public function execute(): void
    {
        $sql = "CREATE TABLE IF NOT EXISTS " . self::DB_TABLE_NAME . " (
            id SERIAL PRIMARY KEY,
            name varchar(256),
            surname varchar(256),
            email varchar(256) UNIQUE)";
        $result = $this->db->query($sql);
    }
}