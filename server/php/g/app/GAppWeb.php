<?php

namespace g\app;

/**
 * @author eoe2005@qq.com
 */
class GAppWeb extends GApp {
    protected function beforeRun() {        
        $this->routeByStr($_GET['__action__']);
    }

    protected function error404() {
        echo "Error";
    }

}
