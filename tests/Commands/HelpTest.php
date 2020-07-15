<?php

namespace Commands;

use PHPUnit\Framework\TestCase;

class HelpTest extends TestCase
{
    public function testExecute()
    {
        $helpCommand = new Help();
        $this->expectOutputString(Help::USAGE);
        $helpCommand->execute();
    }
}
