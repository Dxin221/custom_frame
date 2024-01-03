<?php
namespace fastphp;

//框架根目录
defined('CORE_PATH') or define('CORE_PATH', dirname(__DIR__));

//fastphp框架核心
class Fastphp
{
    //配置内容
    protected $config = [];

    public function __construct($config)
    {
        //初始化配置
        $this->config = $config;
    }

    //运行程序
    public function run()
    {
        spl_autoload_register(array($this, 'loadClass'));
        /*$this->setReporting();
        $this->removeMagicQuotes();
        $this->unregisterGlobals();
        $this->setDbConfig();
        $this->route();*/
    }

    // 自动加载类
    public function loadClass($className)
    {
        echo 'loadClass';exit;
        $classMap = $this->classMap();

        if (isset($classMap[$className])) {
            // 包含内核文件
            $file = $classMap[$className];
        } elseif (strpos($className, '\\') !== false) {
            // 包含应用（application目录）文件
            $file = APP_PATH . str_replace('\\', '/', $className) . '.php';
            if (!is_file($file)) {
                return;
            }
        } else {
            return;
        }

        include $file;

        // 这里可以加入判断，如果名为$className的类、接口或者性状不存在，则在调试模式下抛出错误
    }

    // 内核文件命名空间映射关系
    protected function classMap()
    {
        return [
            'fastphp\base\Controller' => CORE_PATH . '/base/Controller.php',
            'fastphp\base\Model' => CORE_PATH . '/base/Model.php',
            'fastphp\base\View' => CORE_PATH . '/base/View.php',
            'fastphp\db\Db' => CORE_PATH . '/db/Db.php',
            'fastphp\db\Sql' => CORE_PATH . '/db/Sql.php',
        ];
    }
}