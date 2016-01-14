<?php
namespace g\http;
/**
 * @author eoe2005@qq.com
 */
class GSessionCache implements \SessionHandlerInterface {
    protected $_cache;
    protected $_exprire;
    public function __construct(g\cache\GCacheInterface $cache,$_exprire = 7200) {
        $this->_cache = $cache;
        $this->_exprire = $_exprire;
    }

    public function close() {
        return TRUE;
    }

    public function destroy($session_id) {
        return $this->_cache->delete($session_id);
    }

    public function gc($maxlifetime) {
        return TRUE;
    }

    public function open($save_path, $name) {
        return TRUE;
    }

    public function read($session_id) {
        return $this->_cache->get($session_id);
    }

    public function write($session_id, $session_data) {
        return $this->_cache->setExpire($session_id,$session_data,  $this->_exprire);
    }

}
