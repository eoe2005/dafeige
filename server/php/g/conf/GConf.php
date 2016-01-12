<?php

namespace g\conf;

/**
 * @author eoe2005@qq.com
 */
class GConf {

    protected static $_selfs = [];

    /**
     * 
     * @param type $dns
     * @return GConfInteface
     */
    static function ins($dns) {
        $attr = explode('://', $dns);
        $key = md5($dns);
        if (isset(self::$_selfs[$key]) === false) {
            switch (strtolower($attr[0])) {
                case 'ini':
                    self::$_selfs[$key] = new GConfIni($attr[1]);
                    break;
                case 'xml':
                    self::$_selfs[$key] = new GConfXml($attr[1]);
                    break;
                case 'php':
                    self::$_selfs[$key] = new GConfPhp($attr[1]);
                    break;
                case 'json':
                    self::$_selfs[$key] = new GConfJson($attr[1]);
                    break;
                default :
                    throw new \g\exception\GException('参数错误，配置文件不存在');
            }
        }
        return self::$_selfs[$key];
    }

}
