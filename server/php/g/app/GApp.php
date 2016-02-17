<?php

namespace g\app;

/**
 * 应用的一个进本入口文件
 * @author eoe2005@qq.com
 */
abstract class GApp {
    const REQ_TYPE_WEB = 'web';
    const REQ_TYPE_API = 'api';
    const REQ_TYPE_ADMIN = 'admin';
    const REQ_TYPE_REST = 'rest';
    const REQ_TYPE_CONSOLE = 'consoles';
    
    protected $req_type = 'web';

    protected $controllerName = 'Index';
    protected $controllerSubfix = 'Controller';
    protected $actionName = 'index';
    protected $actionSubfix = 'Action';
    protected $apiRoutePath = 'apis';
    protected $restRoutePath = 'rests';
    protected $adminRoutePath = 'admins';
    protected $consoleRoutePath = 'consoles';


    protected $input;
    protected $apiFunc = null;
    protected $controllerDir = 'controllers';
    protected static $app = false;
    protected $appDir = '';
    protected $appConf;
    
    protected $obsCache = [];

    /**
     * 
     * @return GApp
     */
    public static function ins() {
        if (self::$app === FALSE) {
            $cls = get_called_class();
            self::$app = new $cls();
        }
        return self::$app;
    }

    private function __construct() {
        $this->appDir = dirname($_SERVER["DOCUMENT_ROOT"]) . DS;
        $this->input = new GAppInput();
    }

    public function __clone() {
        throw new Exception('对象禁止复制');
    }
    /**
     * 
     * @param type $name
     * @return \g\db\GDBPdo
     */
    public function getDB($name = 'default'){
        $key = 'db.'.$name;
        if(isset($this->obsCache[$key]) === FALSE){
            $conf = $this->appConf->getArray($key);
            //var_dump($conf,$key);
            $dsn = $conf['type'].":";            
            $username = $conf['username'];
            $password = $conf['password'];
            unset($conf['password']);
            unset($conf['username']);
            unset($conf['type']);
            $d = [];
            foreach ($conf AS $k=>$v){
                $d[] = sprintf('%s=%s',$k,$v);
            }
            $dsn .= implode(';', $d);
            $this->obsCache[$key] = \g\db\GDBPdo::ins($dsn, $username, $password);
        }
        return $this->obsCache[$key];
    }
    /**
     * 
     * @param type $name
     * @return \g\cache\GCacheInterface
     */
    public function getCache($name = 'default'){
        $key = 'cache.'.$name;
        if(isset($this->obsCache[$key]) === FALSE){
            $conf = $this->appConf->getArray($key);
            $dns = $conf['type']."://";
            unset($conf['type']);
            $dns .= http_build_query($conf);
            $this->obsCache[$key] = \g\cache\GCache::ins($dns);
        }
        return $this->obsCache[$key];
    }
    /**
     * 加载配置文件
     * @param type $confname
     * @throws Exception
     */
    public function config($confname) {
        $index = strrpos($confname, '.');
        if ($index) {
            $ext = substr($confname, $index + 1);
            $this->appConf = \g\conf\GConf::ins(sprintf('%s://%s%s%s%s', $ext, $this->appDir, 'conf', DS, $confname));
            $autoload = $this->appConf->get('web.autoloadclass.path',null);
            if($autoload){
                $list = explode(',', $autoload);
                foreach($list AS $path){
                    \GAutoLoadClass::addPaths($this->appDir.$path);
                }
            }
            $adminpath = $this->appConf->get('web.admin.prefix',null);
            if($adminpath){
                $this->routeAdmin($adminpath);
            }
            $session = $this->appConf->getArray('web.session',null);
            if($session){
                \g\http\GSession::ins($session);
            }
        } else {
            throw new Exception('参数错误');
        }
        return $this;
    }

    /**
     * 更具字符串解析路由
     * @param type $str
     */
    protected function routeByStr($str) {
        while (strpos($str, '/') === 0) {
            $str = substr($str, 1);
        }
        $prefix = preg_replace(sprintf("/^(%s|%s|%s|%s)\/.*/", $this->apiRoutePath, $this->restRoutePath, $this->adminRoutePath, $this->consoleRoutePath), '$1', $str);
        switch ($prefix) {
            case $this->apiRoutePath:
                $this->controllerDir = 'apis';
                $str = substr($str, strlen($prefix) + 1);
                $this->req_type = self::REQ_TYPE_API;
                break;
            case $this->adminRoutePath;
                $this->controllerDir = 'admins';
                $str = substr($str, strlen($prefix) + 1);
                $this->req_type = self::REQ_TYPE_ADMIN;
                break;
            case $this->restRoutePath:
                $this->controllerDir = 'rests';
                $str = substr($str, strlen($prefix) + 1);
                $this->req_type = self::REQ_TYPE_REST;
                break;
            case $this->consoleRoutePath:
                $this->controllerDir = 'consoles';
                $str = substr($str, strlen($prefix) + 1);
                $this->req_type = self::REQ_TYPE_CONSOLE;
                break;
        }
        $attr = explode('/', $str);
        if (count($attr) > 1) {
            $this->controllerName = ucfirst($attr[0]);
            $action = $attr[1];
            $index = strpos($action, '.');
            if ($index !== FALSE) {
                $action = substr($action, $index);
            }
            $this->actionName = $action;
        }
    }

    public function routeApi($path = 'apis', $func = null) {
        $this->apiRoutePath = $path;
        $this->apiFunc = $func;
        return $this;
    }

    public function routeRest($path = 'rests') {
        $this->restRoutePath = $path;
        return $this;
    }

    public function routeAdmin($path = 'admin') {
        $this->adminRoutePath = $path;
        return $this;
    }

    protected abstract function beforeRun();

    public function run() {
        $this->beforeRun();
        $cls = $this->controllerName . $this->controllerSubfix;
        $file = $this->appDir . $this->controllerDir . DS . $cls . '.php';
        //echo $file;
        if (file_exists($file)) {
            include $file;
            if ($this->apiFunc) {
                $this->input->registerApiFunction($this->apiFunc);
            }
            //var_dump($this->input);
            $obj = new $cls($this->input, $this->appConf,$this->appDir,$this->controllerName,$this->actionName,$this->req_type);
            $mothedName = $this->actionName . $this->actionSubfix;
            
            if (method_exists($obj, $mothedName)) {
                call_user_func_array([$obj, $mothedName], []);
                die();
            }
        }
        $this->error404();
    }

    protected abstract function error404();
}
