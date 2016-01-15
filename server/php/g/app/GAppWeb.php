<?php

namespace g\app;

/**
 * @author eoe2005@qq.com
 */
class GAppWeb extends GApp {
    protected function beforeRun() {
        $this->controllerSubfix = 'Controller';
        $this->actionSubfix = 'Action';
        $this->controllerDir = 'controllers';
        $argv = $_SERVER['argv'];
        $this->routeByStr($_GET['_action_']);
    }

    protected function error404() {
        
    }

}
