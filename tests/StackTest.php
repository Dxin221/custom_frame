<?php
/**
 * 1、composer 安装Monolog日志扩展，安装phpunit单元测试扩展包
 * 2、引入autoload.php文件
 * 3、测试案例
 */
namespace tests;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../vendor/autoload.php';
define("ROOT_PATH", dirname(__DIR__).'/');

class StackTest extends TestCase
{
    public function testPushAndPop()
    {
        $stack = array();
        $this->assertEquals(0,count($stack));

        array_push($stack, 'foo');

        //添加日志文件,如果没有安装monolog，则有关monolog的代码都可以注释掉
        $this->Log()->error('hello',$stack);

        $this->assertEquals('fooa', $stack[count($stack)-1]);
        $this->assertEquals(1,count($stack));

        $this->assertEquals('foo', array_pop($stack));
        $this->assertEquals(0,count($stack));
    }

    public function Log()
    {
        //create a log channel
        $log = new Logger('Tester');
        $log->pushHandler(new StreamHandler(ROOT_PATH . 'storage/logs/app.log', Logger::WARNING));
        $log->error("Error");
        return $log;
    }
}