<?php

namespace Commands;

use PHPUnit\Framework\TestCase;

class CreateTableTest extends TestCase
{
    public function testExecute()
    {
        $mockDb = $this->createMock(\PDO::class);
        $mockDb->expects($this->once())
            ->method('query')
            ->with(
                $this->stringContains('CREATE TABLE')
            );
        $createTableCommand = new CreateTable($mockDb);
        $createTableCommand->execute();
    }
}
