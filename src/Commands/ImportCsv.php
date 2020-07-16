<?php


namespace Commands;


use Exceptions\InvalidFileException;

/**
 * Class ImportCsv
 * Imports the given CSV file and inserts the entries to the database
 * @package Commands
 */
class ImportCsv extends Command
{
    // Number of columns in the CSV
    private const NUM_COLS = 3;
    // Column number of each field in the CSV
    private const COL_NUM_FOR_NAME = 0;
    private const COL_NUM_FOR_SURNAME = 1;
    private const COL_NUM_FOR_EMAIL = 2;

    /**
     * @var \PDO $db
     */
    private $db;
    /**
     * @var string $csvFile
     */
    private $csvFile;
    /**
     * @var bool $skipFirstLine Whether or not to skip the first line (e.g.: the first line is the heading)
     */
    private $skipFirstLine = true;

    /**
     * @var bool $isDryRun Whether or not this is a dry run, in which case no db inserts will be done
     */
    private $isDryRun = false;


    /**
     * ImportCsv constructor.
     * @param \PDO $db
     * @param string $csvFile
     */
    public function __construct(\PDO $db, string $csvFile)
    {
        $this->db = $db;
        $this->csvFile = $csvFile;
    }

    /**
     * @param bool $isDryRun
     */
    public function setIsDryRun(bool $isDryRun)
    {
        $this->isDryRun = $isDryRun;
    }

    /**
     * @return bool
     */
    public function getIsDryRun(): bool
    {
        return $this->isDryRun;
    }

    /**
     * @throws InvalidFileException
     */
    public function execute(): void
    {
        $fp = fopen($this->csvFile, "r");

        if (false === $fp) {
            throw new InvalidFileException("Cannot open file for reading.");
        }

        // Create a prepared statement
        $sql = "INSERT INTO " . DB_TABLE_NAME . " (
            " . DB_FIELD_NAME . ", " . DB_FIELD_SURNAME . ", " . DB_FIELD_EMAIL . ")
            VALUES (:name, :surname, :email)";

        $stmt = $this->db->prepare($sql);

        // Read through the CSV file line by line
        for ($i=0; $row = fgetcsv($fp); $i++) {
            // First line may be the column names that we want to skip
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

            // Check if email is valid
            if (!$this->isValidEmail($email)) {
                $this->errors[] = "Invalid entry in line " . ($i + 1) . ": Invalid email: {$email}.";
                continue;
            }

            // NOTE: The requirements did not mention about validating the names, which could be a business
            // decision. Would go back and ask / confirm.

            // If this is not a dry run, insert the entry to the db
            if (!$this->isDryRun) {
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
        }

        fclose ($fp);
    }

    /**
     * Checks whether the given email address is valid
     * @param string $email
     * @return bool
     */
    private function isValidEmail(string $email): bool
    {
        // Assumes PHP's filter is sufficient. Would check whether a stricter validation is necessary.
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    /**
     * Gets the format of the name / surname that is ready to be inserted to the db
     * The algorithm was taken online from someone else (refer to @author), with some slight modifications.
     *
     * @author Armand Niculescu https://www.media-division.com/correct-name-capitalization-in-php/
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

    /**
     * Gets the format of the email address that is ready to be inserted into the db
     * @param string $email
     * @return string
     */
    private function decorateEmail(string $email): string
    {
        return strtolower($email);
    }
}