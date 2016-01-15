<?php
namespace g\app;
/**
 * @author eoe2005@qq.com
 */
class GAppWebRest extends GApp{
    protected function beforeRun() {
        $this->controllerSubfix = 'Rest';
        $this->actionSubfix = '';
        $this->controllerDir = 'rests';
        $this->routeByStr($_GET['_action_']);
    }

    protected function error404() {
        
    }

}
