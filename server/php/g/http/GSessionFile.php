<?php

namespace g\http;

/**
 * @author eoe2005@qq.com
 */
class GSessionFile implements \SessionHandlerInterface {

    protected $_SESSION_PATH;

    public function __construct($sessionPath = null) {
        if ($sessionPath) {
            $path = dirname($sessionPath) . DS . basename($sessionPath) . DS;
            if (is_dir($path) AND is_writeable($path)) {
                $this->_SESSION_PATH = $path;
            } else {
                throw new g\exception\GException($path . '不存在或者不可写');
            }
        } else {
            
        }
    }

    public function close() {
        return TRUE;
    }

    public function destroy($session_id) {
        if (file_exists($this->_SESSION_PATH . $session_id)) {
            return unlink($this->_SESSION_PATH . $session_id);
        }
        return true;
    }

    public function gc($maxlifetime) {
        return true;
    }

    public function open($save_path, $name) {
        return TRUE;
    }

    public function read($session_id) {
        if (file_exists($this->_SESSION_PATH . $session_id)) {
            return file_get_contents($this->_SESSION_PATH . $session_id);
        }
        return '';
    }

    public function write($session_id, $session_data) {
        return file_put_contents($this->_SESSION_PATH . $session_id, $session_data);
    }

}
