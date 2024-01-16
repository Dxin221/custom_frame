<?php

namespace tests;

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/Calculator.php';

class CalculatorTest extends TestCase
{
    public function testSum()
    {
        $obj = new Calculator();
        $this->assertEquals(1, $obj->sum(0, 0));
    }
}