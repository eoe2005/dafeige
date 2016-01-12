<?php

namespace g\cache;

/**
 * @author eoe2005@qq.com
 */
interface GCacheInterface {

    public function get($k, $def = NULL);

    public function set($k, $v);

    public function setExpire($k, $v, $expire);

    public function delete($keys);

    public function incr($k);

    public function incrBy($k, $step = 1);

    public function decr($k);

    public function decrBy($k, $step = -1);

    public function expire($k, $exprie);

    //针对hash的处理
    public function hGet($k, $f, $def = null);

    public function hSet($k, $f, $val);

    public function hDel($k, $f);

    public function hSetArray($k, $data);

    public function hKeys($k);

    public function hVals($k);

    public function hGetAll($k);

    //针对数组的操作    
    public function lPop($k);

    public function lLen($k);

    public function lPush($k, $v);

    public function lGet($k, $index = 0);

    public function lSet($k, $index = 0, $val);

    public function lGetAll($k);

    public function flush();

    public function delHash($k);

    public function delList($k);
}
