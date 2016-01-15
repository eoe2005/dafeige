<?php

namespace g\app;

/**
 * @author eoe2005@qq.com
 */
class GAppController {
    protected $input;
    public function __construct(g\app\GAppInput $input, g\conf\GConfInteface $confg) {
        $this->input = $input;
        $this->appConf = $confg;
    }
    /**
     * 404 错误页面
     * @param type $name
     */
    protected function error404($name){
        
    }
    public function __call($name, $arguments) {
        $this->error404($name);
    }
   
    
}
