<?php

namespace g\conf;

/**
 * @author eoe2005@qq.com
 */
class GConfJson extends GConfInteface {

    public function __construct($file) {
        if (file_exists($file)) {
            $this->data = json_decode(file_get_contents($file),true);
        } else {
            throw new \g\exception\GException($file . '配置文件不存在!');
        }
    }

}
