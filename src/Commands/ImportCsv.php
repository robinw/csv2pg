<?php


namespace Commands;


use Exceptions\InvalidFileException;

class ImportCsv extends Command
{
    private const NUM_COLS = 3;
    private const COL_NUM_FOR_NAME = 0;
    private const COL_NUM_FOR_SURNAME = 1;
    private const COL_NUM_FOR_EMAIL = 2;

    private $db;
    private $csvFile;
    private $skipFirstLine = true;

    public function __construct(\PDO $db, string $csvFile)
    {
        $this->db = $db;
        $this->csvFile = $csvFile;
    }

    public function execute(): void
    {
        $fp = fopen($this->csvFile, "r");

        if (false === $fp) {
            throw new InvalidFileException("Cannot open file for reading.");
        }

        $sql = "INSERT INTO " . DB_TABLE_NAME . " (
            " . DB_FIELD_NAME . ", " . DB_FIELD_SURNAME . ", " . DB_FIELD_EMAIL . ")
            VALUES (:name, :surname, :email)";

        $stmt = $this->db->prepare($sql);

        for ($i=0; $row = fgetcsv($fp); $i++) {
            if ($this->skipFirstLine && 0 === $i) {
                continue;
            }

            if (self::NUM_COLS !== count($row)) {
                echo "Invalid entry in line " . ($i + 1). ": wrong number of fields.";
            }

            $name = $row[self::COL_NUM_FOR_NAME];
            $surname = $row[self::COL_NUM_FOR_SURNAME];
            $email = $row[self::COL_NUM_FOR_EMAIL];

            $stmt->execute(
                array(
                    ':name' => $name,
                    ':surname' => $surname,
                    ':email' => $email
                )
            );
        }

        fclose ($fp);
    }
}