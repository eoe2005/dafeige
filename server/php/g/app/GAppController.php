<?php

namespace g\app;

/**
 * @author eoe2005@qq.com
 */
class GAppController {
    protected $input;
    protected $appConf;
    protected $appDir;
    protected $controllerName;
    protected $actionName;
    protected $reqType;
    public function __construct(\g\app\GAppInput $input, \g\conf\GConfInteface $confg,$appdir = '',$controllerName = '',$actionName = '',$reqtype = 'web') {
        $this->input = $input;
        $this->appConf = $confg;
        $this->appDir = $appdir;
        $this->controllerName = $controllerName;
        $this->actionName = $actionName;
        $this->reqType = $reqtype;
    }
    /**
     * 404 错误页面
     * @param type $name
     */
    protected function error404($name){
        echo "页面不存在".$name;
    }
    public function __call($name, $arguments) {
        $this->error404($name);
    }
    /**
     * 获取模板生成的内容
     * @param type $args
     * @param type $tname
     */
    protected function fetchTemplate($tname = null,$args = []){
        ob_start();
        
        $file = $this->appDir.'views'.DS.$this->reqType.DS;
        if($tname){
            if(strpos($tname, '/') !== false){
                $file .= strtolower($tname).'.php';
            }else{
                $file .= strtolower($this->controllerName).DS.strtolower($tname).'.php';
            }
        }else{
            $file .= strtolower($this->controllerName).DS.strtolower($this->actionName).'.php';
        }
        extract($args);
        include $file;
        return ob_get_clean();
    }
    /**
     * 显示页面
     * @param type $args
     * @param type $tname
     */
    protected function display($tname = null,$args = []){
        echo $this->fetchTemplate($tname,$args);
    }
    /**
     * 显示模板的内容
     * @param type $args
     * @param type $tname
     * @param type $layoutname
     */
    protected function layout($tname = null,$args = [],$layoutname = 'main'){
        $__contents__ = $this->fetchTemplate($tname,$args);
        extract($args);
        $file = $this->appDir.'views'.DS.$this->reqType.DS.'layout'.DS.$layoutname.'.php';
        include $file;
    }
}
