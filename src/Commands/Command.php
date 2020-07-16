<?php


namespace Commands;

/**
 * Class Command
 * Parent class for all commands
 *
 * @package Commands
 */
abstract class Command
{
    /**
     * @var array List of errors (if any)
     */
    protected $errors = array();

    /**
     * Executes the command
     */
    abstract public function execute() :void;

    /**
     * Checks whether or not there were any errors after executing the command
     * @return bool
     */
    public function hasError(): bool
    {
        return (count($this->errors) > 0);
    }

    /**
     * Gets list of errors that occurred after executing the command
     * @return array List of errors, an empty array if no errors.
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}