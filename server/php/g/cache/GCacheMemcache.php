<?php

namespace g\cache;

/**
 * @author eoe2005@qq.com
 */
class GCacheMemcache implements \g\cache\GCacheInterface {

    protected $memcache;
    protected $master_hash = 'hash_';
    protected $master_list = 'list_';

    public function __construct($servers, $key = null) {
        if ($key) {
            $this->memcache = new \Memcached($key);
        } else {
            $this->memcache = new \Memcached();
        }
        $list = $this->memcache->getServerList();
        if (count($list) == 0) {
            $this->memcache->setOptions([
                Memcached::OPT_COMPRESSION => TRUE,
                Memcached::OPT_SERIALIZER => Memcached::SERIALIZER_PHP,
                Memcached::OPT_DISTRIBUTION => Memcached::DISTRIBUTION_CONSISTENT,
                Memcached::OPT_BINARY_PROTOCOL => TRUE,
                Memcached::OPT_NO_BLOCK => TRUE,
                Memcached::OPT_TCP_NODELAY => TRUE
            ]);
            $this->memcache->addServers($servers);
        }
    }

    public function __call($name, $arguments) {
        if (method_exists($this->memcache, $name)) {
            return call_user_func_array([$this->memcache, $name], $arguments);
        } else {
            throw new g\exception\GException('方法不存在');
        }
    }

    public function decr($k) {
        return $this->decrBy($k);
    }

    public function decrBy($k, $step = -1) {
        return $this->memcache->decrement($k, $step);
    }

    public function delete($keys) {
        //@todo 需要删除List和Hash的数据
        if (is_array($keys)) {
            return $this->memcache->deleteMulti($keys);
        }
        return $this->memcache->delete($keys);
    }

    public function expire($k, $exprie) {
        return $this->memcache->touch($k, $exprie);
    }

    public function get($k, $def = NULL) {
        $ret = $this->memcache->get($k);
        if ($ret === FALSE) {
            return $def;
        }
        return $ret;
    }

    private function buildHKey($k, $f) {
        return sprintf('h_%s_%s', $k, $f);
    }

    private function masterHkey($k) {
        return sprintf('%s%s', $this->master_hash, $k);
    }

    public function hDel($k, $f) {
        return $this->delete($this->buildHKey($k, $f));
    }

    public function hGet($k, $f, $def = null) {
        $ret = $this->get($this->buildHKey($k, $f));
        if ($ret === false) {
            return $def;
        }
        return $ret;
    }

    public function hGetAll($k) {
        $keys = $this->hKeys($this->masterHkey($k));
        $ret = [];
        foreach ($keys AS $key) {
            $ret[$key] = $this->hGet($k, $key);
        }
        return $ret;
    }

    public function hKeys($k) {
        return $this->get($this->masterHkey($k), []);
    }

    public function hSet($k, $f, $val) {
        $keys = $this->hKeys($k);
        $keys[] = $f;
        $keys = array_unique($keys);
        $this->set($this->masterHkey($k), $keys);
        return $this->set($this->buildHKey($k, $f), $val);
    }

    public function hSetArray($k, $data) {
        $this->set($k, array_keys($data));
        foreach ($data AS $f => $val) {
            $this->set($this->buildHKey($k, $f), $val);
        }
        return true;
    }

    public function hVals($k) {
        $data = $this->hGetAll($k);
        $ret = array_values($data);
        return array_unique($ret);
    }

    public function incr($k) {
        return $this->incrBy($k);
    }

    public function incrBy($k, $step = 1) {
        return $this->memcache->increment($k, $step);
    }

    private function bulidListKey($k, $index) {
        return sprintf('l_%s_%d', $k, $index);
    }

    private function masterListKey($key) {
        return sprintf('%s%s', $this->master_list, $key);
    }

    public function lGet($k, $index = 0) {
        return $this->get($this->bulidListKey($k, $index));
    }

    public function lGetAll($k) {
        $len = $this->lLen($k);
        $ret = [];
        for ($i = 0; $i < $len; $i++) {
            $ret[$i] = $this->lGet($k, $i);
        }
        return $ret;
    }

    public function lLen($k) {
        $key = $this->masterListKey($k);
        return $this->get($key, 0);
    }

    public function lPop($k) {
        $key = $this->masterListKey($k);
        $len = $this->get($key, 0) - 1;
        if ($len >= 0) {
            $this->decr($key);
            $ret = $this->lGet($k, $len);
            $this->delete($this->bulidListKey($k, $len));
            return $ret;
        }
        return null;
    }

    public function lPush($k, $v) {
        $index = $this->incr($this->masterListKey($k));
        return $this->set($this->bulidListKey($k, $index - 1), $v);
    }

    public function lSet($k, $index = 0, $val) {
        $len = $this->lLen($k);
        if ($len > $index) {
            return $this->set($this->bulidListKey($k, $index), $val);
        }
        return false;
    }

    public function set($k, $v) {
        return $this->setExpire($k, $v);
    }

    public function setExpire($k, $v, $expire = 0) {
        return $this->memcache->set($k, $v, $expire);
    }

    public function flush() {
        return $this->memcache->flush();
    }

    public function delHash($k) {
        $keys = $this->hKeys($k);
        foreach ($keys AS $f) {
            $this->hDel($k, $f);
        }
        $key = $this->masterHkey($k);
        return $this->delete($key);
    }

    public function delList($k) {
        while ($this->lPop($k)) {
            
        }
        return true;
    }

}
