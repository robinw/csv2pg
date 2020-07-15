<?php


namespace Commands;


class Help extends Command
{
    public const USAGE = <<<USAGE
Accepted arguments:
• --file [csv file name] – this is the name of the CSV to be parsed
• --create_table – this will cause the PostgreSQL users table to be built (and no further action will be taken)
• --dry_run – this will be used with the --file directive in case we want to run the script but not insert into the DB. All other functions will be executed, but the database won't be altered
• -u – PostgreSQL username
• -p – PostgreSQL password
• -h – PostgreSQL host
• --help – Output this help message.
USAGE;

    public function execute(): void
    {
        echo self::USAGE;
    }
}