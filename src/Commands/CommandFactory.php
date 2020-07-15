<?php

namespace Commands;


use Exceptions\InvalidFileException;
use PDO;
use PDOException;

class CommandFactory
{
    public const DB_USER_OPTION = "u";
    public const DB_PASSWORD_OPTION = "p";
    public const DB_HOST_OPTION = "h";

    public const CREATE_TABLE_COMMAND = "create_table";
    public const FILE_COMMAND = "file";
    public const DRY_RUN_COMMAND = "dry_run";
    public const HELP_COMMAND = "help";


    public function getCommand($options): ?Command
    {
        $command = null;

        if (array_key_exists(self::HELP_COMMAND, $options)) {
            $command = new Help();
        }
        else if (array_key_exists(self::CREATE_TABLE_COMMAND, $options)) {

            try {
                $db = $this->getDb($options);
            } catch (PDOException $e) {
                throw $e;
            }

            $command = new CreateTable($db);
        }
        else if (array_key_exists(self::FILE_COMMAND, $options)) {
            $fileName = $options[self::FILE_COMMAND];

            if (!file_exists($fileName)) {
                throw new InvalidFileException("File {$fileName} does not exist or is not readable.");
            }

            try {
                $db = $this->getDb($options);
            } catch (PDOException $e) {
                throw $e;
            }

            $command = new ImportCsv($db, $fileName);
            if (array_key_exists(self::DRY_RUN_COMMAND, $options)) {
                $command->setIsDryRun(true);
            }
        }

        return $command;
    }

    private function getDb($options): PDO
    {
        $dbUser = array_key_exists(self::DB_USER_OPTION, $options) ? $options[self::DB_USER_OPTION] : null;
        $dbPwd = array_key_exists(self::DB_PASSWORD_OPTION, $options) ? $options[self::DB_PASSWORD_OPTION] : null;
        $dbHost = array_key_exists(self::DB_HOST_OPTION, $options) ? $options[self::DB_HOST_OPTION] : null;

        $dsn = DB_TYPE . ':dbname=' . DB_NAME . ';host=' . $dbHost;
        $db = null;

        return new PDO($dsn, $dbUser, $dbPwd);
    }
}