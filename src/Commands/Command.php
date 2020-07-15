<?php


namespace Commands;


abstract class Command
{
    protected $errors = array();

    abstract public function execute() :void;

    public function hasError(): bool
    {
        return (count($this->errors) > 0);
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}