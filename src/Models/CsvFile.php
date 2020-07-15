<?php
namespace Models;

class CsvFile
{
    private $fileName;

    public function __construct(string $fileName)
    {
        $this->fileName = $fileName;
    }
}