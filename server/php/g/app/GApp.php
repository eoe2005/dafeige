<?php

namespace g\app;

/**
 * 应用的一个进本入口文件
 * @author eoe2005@qq.com
 */
abstract class GApp {
    protected $controllerName = 'Index';      
    protected $controllerSubfix = 'Controller';
    protected $actionName = 'index';  
    protected $actionSubfix = 'Action';
    
    protected $input;


    protected $controllerDir = 'controller';
    protected static $app = false;
    protected $appDir= '';
    protected $appConf;

    /**
     * 
     * @return GApp
     */
    public static function ins(){
        if(self::$app === FALSE){
            $cls = get_called_class();
            self::$app = new $cls();
        }
        return self::$app;
    }
    
    private function __construct() {
        $this->appDir = dirname($_SERVER["DOCUMENT_ROOT"]).DS;        
        \GAutoLoadClass::addPaths($this->appDir.'models'.DS);
        $this->input =  new GAppInput();
    }
    public function __clone() {
        throw new Exception('对象禁止复制');
    }
    /**
     * 加载配置文件
     * @param type $confname
     * @throws Exception
     */
    public function config($confname){
        $index = strrpos($confname, '.');
        if($index){
            $ext = substr($confname, $index + 1);
            $this->appConf = \g\conf\GConf::ins(sprintf('%s://%s%s%s%s',$ext,$this->appDir,'conf',DS,$confname));
        }else{
            throw new Exception('参数错误');
        }
    }
    /**
     * 更具字符串解析路由
     * @param type $str
     */
    protected function routeByStr($str){
        while (strpos($str, '/') === 0){
            $str = substr($str, 1);
        }
        $attr = explode('/', $str);
        if(count($attr) > 1){
            $this->controllerName = ucfirst($attr[0]);
            $action = $attr[1];
            $index = strpos($action, '.');
            if($index !== FALSE){
                $action = substr($action, $index);
            }
            $this->actionName = $action;
        }
    }
    /**
     * 根据参数设置路由
     * @param type $controller
     * @param type $action
     */
    protected function routeByParamese($controller,$action){
        $this->controllerName = ucfirst(trim($controller));
        $this->actionName = trim($action);
    }

    protected abstract function beforeRun();

    public function run(){
        $this->beforeRun();
        $cls = $this->controllerName.$this->controllerSubfix;
        $file = $this->appDir.$this->controllerDir.DS.$cls.'.php';
        if(file_exists($file)){
            include $file;
            $obj = new $cls($this->input,$this->appConf);
            $mothedName = $this->actionName.$this->actionSubfix;
            if(method_exists($obj, $mothedName)){
                call_user_func_array([$obj,$mothedName],[]);
                die();
            }
        }
        $this->error404();
    }
    protected abstract function error404();
}
