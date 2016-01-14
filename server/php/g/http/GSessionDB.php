<?php

namespace g\http;

/**
 * @author eoe2005@qq.com
 */
class GSessionDB implements \SessionHandlerInterface {
    protected $_db;
    protected $_tname = 't_session';
    public function __construct(g\db\GDBPdo $db) {
        $this->_db = $db;
    }

    public function close() {
        return TRUE;
    }

    public function destroy($session_id) {
        
    }

    public function gc($maxlifetime) {
        
    }

    public function open($save_path, $name) {
        
    }

    public function read($session_id) {
        //return $this->_db->get
    }

    public function write($session_id, $session_data) {
        //return $this->_db->
    }

}
