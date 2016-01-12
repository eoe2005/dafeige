<?php

namespace g\cache;

/**
 * @author eoe2005@qq.com
 */
class GCache {

    protected static $_caches = [];

    public static function ins($dns) {
        $key = md5($dns);
        if (isset(self::$_caches[$key]) === FALSE) {
            $index = strpos($dns, '://');
            $model = null;
            if ($index) {
                $model = strtolower(substr($dns, 0, $index));
                $dns = substr($dns, $index+3);
            }
            parse_str($dns,$set);
            switch ($model) {
                case 'redis':
                    self::$_caches[$key] = new GCacheRedis($set['host'],(isset($set['db']) ? $set['db'] : 0),(isset($set['auth']) ? $set['auth'] : null));
                    break;
                case 'memcached':
                    $host = explode(',', $set['hosts']);
                    $hosts = [];
                    foreach ($host AS $h){
                        $hosts[] = explode(':', $h);
                    }
                    self::$_caches[$key] = new GCacheMemcache($hosts,(isset($set['key']) ? $set['key'] : null));
                    break;
                default :
                    throw new \g\exception\GException('不支持的缓存模式');
            }
        }
        return self::$_caches[$key];
    }

}
