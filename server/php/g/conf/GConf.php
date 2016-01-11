<?php
namespace g\conf;
/**
 * @author eoe2005@qq.com
 */
class GConf {
    /**
     * 
     * @param type $dns
     * @return GConfInteface
     */
    static function ins($dns){
        $attr = explode('://', $dns);
        switch (strtolower($attr[0])){
            case 'ini':
                return new GConfIni($attr[1]);
                break;
            case 'xml':
                return new GConfXml($attr[1]);
                break;
            case 'php':
                return new GConfPhp($attr[1]);
                break;
            case 'json':
                return new GConfJson($attr[1]);
                break;
        }
        throw  new \g\exception\GException('参数错误，配置文件不存在');
    }
}
