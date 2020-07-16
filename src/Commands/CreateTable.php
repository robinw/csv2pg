<?php


namespace Commands;

/**
 * Class CreateTable
 * Creates the database table if it does not exist yet
 *
 * @package Commands
 */
class CreateTable extends Command
{
    private $db;

    public function __construct(\PDO $db)
    {
        $this->db = $db;
    }

    /**
     * Creates the user table if it does not exist yet.
     */
    public function execute(): void
    {
        /*
         * In this scenario we opt for the safer option where we only create the table if it doesn't exist.
         * If we want to always create a new one and drop any old one we would first drop the table if it exists
         * and then create the table. May also want to wrap these in a transaction so that if the table creation
         * fails we can keep the old table.
         */
        $sql = "CREATE TABLE IF NOT EXISTS " . DB_TABLE_NAME . " (
            id SERIAL PRIMARY KEY,
            " . DB_FIELD_NAME . " varchar(256),
            " . DB_FIELD_SURNAME . " varchar(256),
            " . DB_FIELD_EMAIL . " varchar(256) UNIQUE)";
        $result = $this->db->query($sql);

        // We may want to do something if it fails
    }
}