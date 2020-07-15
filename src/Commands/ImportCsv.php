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
                $this->errors[] = "Invalid entry in line " . ($i + 1). ": Wrong number of fields.";
                continue;
            }

            $name = trim($row[self::COL_NUM_FOR_NAME]);
            $surname = trim($row[self::COL_NUM_FOR_SURNAME]);
            $email = trim($row[self::COL_NUM_FOR_EMAIL]);

            if (!$this->isValidEmail($email)) {
                $this->errors[] = "Invalid entry in line " . ($i + 1) . ": Invalid email: {$email}.";
                continue;
            }

            $result = $stmt->execute(
                array(
                    ':name' => $this->decorateName($name),
                    ':surname' => $this->decorateName($surname),
                    ':email' => $this->decorateEmail($email)
                )
            );

            if (false === $result) {
                $this->errors[] = "Failed inserting entry in line " . ($i + 1) . ": Error " . implode(" - ", $stmt->errorInfo());
            }
        }

        fclose ($fp);
    }

    private function isValidEmail(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    /**
     *
     * Credit: https://www.media-division.com/correct-name-capitalization-in-php/
     * @param string $name
     * @return string
     */
    private function decorateName(string $name): string
    {
        $wordSplitters = array(' ', '-', "O'", "L'", "D'", 'St.', 'Mc');
        $lowercaseExceptions = array('the', 'van', 'den', 'von', 'und', 'der', 'de', 'da', 'of', 'and', "l'", "d'");
        $uppercaseExceptions = array('III', 'IV', 'VI', 'VII', 'VIII', 'IX');

        $decoratedName = strtolower($name);
        foreach ($wordSplitters as $delim) {
            $words = explode($delim, $decoratedName);
            $newWords = array();
            foreach ($words as $w) {
                if (in_array(strtoupper($w), $uppercaseExceptions)) {
                    $newWords[] = strtoupper($w);
                } else if (!in_array($w, $lowercaseExceptions)) {
                    $newWords[] = ucfirst($w);
                }
            }

            if (in_array(strtolower($delim), $lowercaseExceptions)) {
                $delim = strtolower($delim);
            }

            $decoratedName = join($delim, $newWords);
        }

        return $decoratedName;
    }

    private function decorateEmail(string $email): string
    {
        return strtolower($email);
    }
}