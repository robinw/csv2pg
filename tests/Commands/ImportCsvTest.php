<?php

namespace Commands;

use PHPUnit\Framework\TestCase;

class ImportCsvTest extends TestCase
{
    private const CSV_FILE_NO_ERRORS = __DIR__ . '/../test_files/file_with_no_errors.csv';
    private const CSV_FILE_WITH_ONE_ERROR = __DIR__ . '/../test_files/file_with_1_error.csv';


    public function testSetIsDryRun()
    {
        $db = $this->createMock(\PDO::class);
        $importCsv = new ImportCsv($db, 'fileName');
        $importCsv->setIsDryRun(true);
        $this->assertTrue($importCsv->getIsDryRun(), "Setting 'is dry run' to true failed.");
        $importCsv->setIsDryRun(false);
        $this->assertFalse($importCsv->getIsDryRun(), "Setting 'is dry run' to false failed.");
    }

    public function testExecuteWhenAllEntriesAreValid()
    {
        $db = $this->createMock(\PDO::class);
        $importCsv = new ImportCsv($db, self::CSV_FILE_NO_ERRORS);

        $stmt= $this->createMock(\PDOStatement::class);
        $db->method('prepare')
            ->will($this->returnValue($stmt));

        // There are 10 entries in the test CSV files, all valid
        $stmt->expects($this->exactly(10))
            ->method('execute');

        $importCsv->execute();

        $this->assertFalse($importCsv->hasError());
    }

    public function testExecuteWhenOneEntryIsInvalid()
    {
        $db = $this->createMock(\PDO::class);
        $importCsv = new ImportCsv($db, self::CSV_FILE_WITH_ONE_ERROR);

        $stmt= $this->createMock(\PDOStatement::class);
        $db->method('prepare')
            ->will($this->returnValue($stmt));

        // There are 11 entries in the test CSV file, 1 of which is invalid
        $stmt->expects($this->exactly(10))
            ->method('execute');

        $importCsv->execute();

        $this->assertTrue($importCsv->hasError());
        $this->assertCount(1, $importCsv->getErrors());
    }
}
