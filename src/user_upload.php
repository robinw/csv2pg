<?php

use Commands\Command;
use Commands\CommandFactory;

require_once "../autoload.php";
require_once "config.php";


$short_options =
    CommandFactory::DB_USER_OPTION . "::" .
    CommandFactory::DB_PASSWORD_OPTION . "::" .
    CommandFactory::DB_HOST_OPTION . "::";

$long_options = [
    CommandFactory::CREATE_TABLE_COMMAND . "::",
    CommandFactory::FILE_COMMAND . "::",
    CommandFactory::DRY_RUN_COMMAND . "::",
    CommandFactory::HELP_COMMAND];

$options = getopt($short_options, $long_options);

$command = (new CommandFactory())->getCommand($options);
if ($command instanceof Command) {
    $command->execute();
}