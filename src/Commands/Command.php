<?php


namespace Commands;


abstract class Command
{
    const DB_TABLE_NAME = "users";

    abstract public function execute() :void;
}