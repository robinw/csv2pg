<?php

use Commands\Help;

require_once "../autoload.php";


const DB_USER_OPTION = "u";
const DB_PASSWORD_OPTION = "p";
const DB_HOST_OPTION = "h";

const CREATE_TABLE_COMMAND = "create_table";
const FILE_COMMAND = "file";
const DRY_RUN_COMMAND = "dry_run";
const HELP_COMMAND = "help";


$short_options =
    DB_USER_OPTION . "::" .
    DB_PASSWORD_OPTION . "::" .
    DB_HOST_OPTION . "::";

$long_options = [
    CREATE_TABLE_COMMAND . "::",
    FILE_COMMAND . "::",
    DRY_RUN_COMMAND . "::",
    HELP_COMMAND];

$options = getopt($short_options, $long_options);

if (array_key_exists(HELP_COMMAND, $options)) {
    $helpCommand = new Help();
    $helpCommand->execute();
}
