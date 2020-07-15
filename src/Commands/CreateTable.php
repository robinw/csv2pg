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
        $sql = "CREATE TABLE IF NOT EXISTS " . DB_TABLE_NAME . " (
            id SERIAL PRIMARY KEY,
            " . DB_FIELD_NAME . " varchar(256),
            " . DB_FIELD_SURNAME . " varchar(256),
            " . DB_FIELD_EMAIL . " varchar(256) UNIQUE)";
        $result = $this->db->query($sql);
    }
}