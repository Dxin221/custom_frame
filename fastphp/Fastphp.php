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
//        $this->setReporting();
//        $this->removeMagicQuotes();
//        $this->unregisterGlobals();
//        $this->setDbConfig();
        $this->route();
    }

    // 路由处理
    public function route()
    {
        $controllerName = $this->config['defaultController'];
        $actionName     = $this->config['defaultAction'];
        $param          = array();

        $url = $_SERVER['REQUEST_URI'];
        //清除？之后的内容
        if (false !== $pos = strpos($url, '?')) {
            $url = substr($url, 0, $pos);
        }
        //删除前后的'/'
        $url = trim($url, '/');

        if ($url) {
            //使用“/”分割字符串，并保存在数组中
            $urlArray = explode('/', $url);
            //删除空的数组元素
            $urlArray = array_filter($urlArray);
            //获取控制器名
            $controllerName = ucfirst($urlArray[0]);
            //获取动作名
            array_shift($urlArray);
            $actionName = $urlArray ? $urlArray[0] : $actionName;
            //获取url参数
            array_shift($urlArray);
            $param = $urlArray ?: array();
        }

        //判断控制器和方法是否存在,class_exists()函数会自动加载类文件
        $controller = 'app\\controllers\\' . $controllerName . 'Controller';
        if (!class_exists($controller)) {
            //控制器不存在
            exit($controller . '控制器不存在');
        }
        if (!method_exists($controller, $actionName)) {
            //方法不存在
            exit($actionName  . '方法不存在');
        }

        // 如果控制器和操作名存在，则实例化控制器，因为控制器对象里面
        // 还会用到控制器名和操作名，所以实例化的时候把他们俩的名称也
        // 传进去。结合Controller基类一起看
        $dispatch = new $controller($controllerName, $actionName);

        // $dispatch保存控制器实例化后的对象，我们就可以调用它的方法，
        // 也可以像方法中传入参数，以下等同于：$dispatch->$actionName($param)
        call_user_func_array(array($dispatch, $actionName), $param);
    }

    // 自动加载类
    public function loadClass($className)
    {
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
            'fastphp\base\Model'      => CORE_PATH . '/base/Model.php',
            'fastphp\base\View'       => CORE_PATH . '/base/View.php',
            'fastphp\db\Db'           => CORE_PATH . '/db/Db.php',
            'fastphp\db\Sql'          => CORE_PATH . '/db/Sql.php',
        ];
    }
}