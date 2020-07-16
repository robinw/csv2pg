<?php

namespace Commands;


use Exceptions\InvalidFileException;
use PDO;
use PDOException;

/**
 * Class CommandFactory
 * Factory class for creating one of the Command subclasses
 * @package Commands
 */
class CommandFactory
{
    /**
     * List of options expected to be specified by user related to database details
     */
    public const DB_USER_OPTION = "u";
    public const DB_PASSWORD_OPTION = "p";
    public const DB_HOST_OPTION = "h";

    /**
     * List of options expected to be specified by user related to the command they wish to run
     */
    public const CREATE_TABLE_COMMAND = "create_table";
    public const FILE_COMMAND = "file";
    public const DRY_RUN_COMMAND = "dry_run";
    public const HELP_COMMAND = "help";


    /**
     * Gets the corresponding Command subclass for the given options
     * @param array $options List of options as an associative array
     * @return Command|null
     * @throws InvalidFileException|PDOException
     */
    public function getCommand(array $options): ?Command
    {
        $command = null;

        // User executes the 'Help' command
        if (array_key_exists(self::HELP_COMMAND, $options)) {
            $command = new Help();
        }
        // User executes the 'Create Table' command
        else if (array_key_exists(self::CREATE_TABLE_COMMAND, $options)) {
            // Try to get the PDO object
            try {
                $db = $this->getDb($options);
            } catch (PDOException $e) {
                // Simply re-throw the exception for now
                throw $e;
            }

            $command = new CreateTable($db);
        }
        // User executes the 'File' command
        else if (array_key_exists(self::FILE_COMMAND, $options)) {
            $fileName = $options[self::FILE_COMMAND];

            // Check that the file does exist
            if (!file_exists($fileName)) {
                throw new InvalidFileException("File {$fileName} does not exist or is not readable.");
            }

            // Try to get the PDO object
            try {
                $db = $this->getDb($options);
            } catch (PDOException $e) {
                // Simply re-throw the exception for now
                throw $e;
            }

            $command = new ImportCsv($db, $fileName);
            // Check if user specified the 'Dry Run' option
            if (array_key_exists(self::DRY_RUN_COMMAND, $options)) {
                $command->setIsDryRun(true);
            }
        }

        return $command;
    }

    /**
     * Gets a PDO object to access the database with
     * @param array $options
     * @return PDO
     */
    private function getDb(array $options): PDO
    {
        $dbUser = array_key_exists(self::DB_USER_OPTION, $options) ? $options[self::DB_USER_OPTION] : null;
        $dbPwd = array_key_exists(self::DB_PASSWORD_OPTION, $options) ? $options[self::DB_PASSWORD_OPTION] : null;
        $dbHost = array_key_exists(self::DB_HOST_OPTION, $options) ? $options[self::DB_HOST_OPTION] : null;

        $dsn = DB_TYPE . ':dbname=' . DB_NAME . ';host=' . $dbHost;

        try {
            return new PDO($dsn, $dbUser, $dbPwd);
        } catch (PDOException $e) {
            throw new PDOException("Could not connect to the database :" . $e->getMessage());
        }
    }
}