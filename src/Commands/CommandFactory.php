<?php

namespace Commands;


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
            $dbUser = array_key_exists(self::DB_USER_OPTION, $options) ? $options[self::DB_USER_OPTION] : null;
            $dbPwd = array_key_exists(self::DB_PASSWORD_OPTION, $options) ? $options[self::DB_PASSWORD_OPTION] : null;
            $dbHost = array_key_exists(self::DB_HOST_OPTION, $options) ? $options[self::DB_HOST_OPTION] : null;

            $dsn = DB_TYPE . ':dbname=' . DB_NAME . ';host=' . $dbHost;
            $db = null;

            try {
                $db = new PDO($dsn, $dbUser, $dbPwd);
            } catch (PDOException $e) {
                throw $e;
            }

            $command = new CreateTable($db);
        }

        return $command;
    }
}