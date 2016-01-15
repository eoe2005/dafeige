<?php
namespace g\app;
/**
 * @author eoe2005@qq.com
 */
class GAppWebApi extends GApp{
    protected function beforeRun() {
        $this->controllerSubfix = 'Api';
        $this->actionSubfix = '';
        $this->controllerDir = 'apis';
        $this->routeByStr($_GET['_action_']);
    }

    protected function error404() {
        
    }

}
