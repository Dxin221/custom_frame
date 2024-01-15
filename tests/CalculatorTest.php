<?php
namespace tests;

//use PHPUnit\Framework\TestCase;

//require_once __DIR__ . '/../vendor/autoload.php';

class CalculatorTest
{
    public function testSum()
    {
        $obj = new \Calculator();
        //$this->assertEquals(0, $obj->sum(0, 0));
        return $obj->sum(0, 0);
    }
}
echo (new CalculatorTest())->testSum();