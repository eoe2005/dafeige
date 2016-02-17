<?php

namespace g\http;

/**
 * @author eoe2005@qq.com
 */
class GSession {

    public static function ins($conf) {
        $type = isset($conf['type']) ? $conf['type'] : null;
        switch ($type) {
            case 'redis':
                $dns = $type . "://";
                unset($conf['type']);
                $expire = $conf['expire'];
                unset($conf['expire']);
                $dns .= http_build_query($conf);
                $cache = \g\cache\GCache::ins($dns);
                //$session_h = new GSessionCache($cache, $expire);
                //$ret = session_set_save_handler($session_h,FALSE);
                //var_dump($ret);
                session_start();
                break;
        }
    }

}
