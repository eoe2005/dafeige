<?php
namespace g\cache;
/**
 * @author eoe2005@qq.com
 */
class GCacheRedis implements g\cache\GCacheInterface {
    protected $redis;
    public function __construct($host,$db = 0,$auth = null) {
        $this->redis = new \Redis();
        call_user_func_array([$this->redis,'pconnect'], $host);
        if($auth){
            $this->redis->auth($auth);
        }
        $this->redis->select($db);
    }

    public function decr($k) {
        return $this->decrBy($k);
    }

    public function decrBy($k, $step = -1) {
        return $this->redis->decrBy($k,$step);
    }

    public function delete($keys) {
        return $this->redis->delete($keys);
    }

    public function expire($k, $exprie) {
        return $this->redis->setTimeout($k,$exprie);
    }

    public function get($k, $def = NULL) {
        $ret = $this->redis->get($k);
        if($ret === FALSE){
            return $def;
        }
        return $ret;
    }

    public function hDel($k, $f) {
        return $this->redis->hDel($k,$f);
    }

    public function hGet($k, $f, $def = null) {
        $ret = $this->redis->hGet($k,$f);
        if($ret === FALSE){
            return $def;
        }
        return $ret;
    }

    public function hGetAll($k) {
        return $this->redis->hGetAll($k);
    }

    public function hKeys($k) {
        return $this->redis->hKeys($k);
    }

    public function hSet($k, $f, $val) {
        return $this->redis->hSet($k,$f,$val);
    }

    public function hSetArray($k, $data) {
        return $this->redis->hSet($k,$data);
    }

    public function hVals($k) {
        return $this->redis->hVals($k);
    }

    public function incr($k) {
        return $this->redis->incr($k);
    }

    public function incrBy($k, $step = 1) {
        return $this->redis->incr($k,$step);
    }

    public function lGet($k, $index = 0) {
        return $this->redis->lGet($k,$index);
    }

    public function lGetAll($k) {
        return $this->redis->lRange($k,0,-1);
    }

    public function lLen($k) {
        return $this->redis->lLen($k);
    }

    public function lPop($k) {
        return $this->redis->lPop($k);
    }

    public function lPush($k, $v) {
        return $this->redis->lPush($k,$v);
    }

    public function lSet($k, $index = 0, $val) {
        return $this->redis->lInsert($k,$index,$val);
    }

    public function set($k, $v) {
        return $this->redis->set($k,$v);
    }

    public function setExpire($k, $v, $expire) {
        return $this->redis->set($k,$v,['nx','ex' => $expire]);
    }

    public function flush() {
        return $this->redis->flushDB();
    }

    public function delHash($k) {
        return $this->redis->delete($k);
    }

    public function delList($k) {
        return $this->redis->delete($k);
    }

}
