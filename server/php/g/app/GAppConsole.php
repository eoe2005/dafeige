<?php
namespace g\app;
/**
 * @author eoe2005@qq.com
 */
class GAppConsole extends GApp{
    protected function beforeRun() {
        $this->controllerSubfix = 'Console';
        $this->actionSubfix = 'Cmd';
        $this->controllerDir = 'consoles';
        $argv = $_SERVER['argv'];
        if(isset($argv[1])){
            $this->routeByStr();
        }else{
            $this->error404();
        }
    }

    protected function error404() {
        echo "参数错误\n";
        die();
    }

}
