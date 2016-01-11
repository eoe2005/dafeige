<?php

namespace g\conf;

/**
 * @author eoe2005@qq.com
 */
class GConfPhp extends GConfInteface {

    public function __construct($file) {
        if (file_exists($file)) {
            $this->data = include $file;
        } else {
            throw new \g\exception\GException($file . '配置文件不存在!');
        }
    }

}
