<?php

namespace g\app;

/**
 * 获取输出的数据内容
 * @author eoe2005@qq.com
 */
class GAppInput {
    /**
     * API提交的数据
     * @var type 
     */
    protected $apiData = [];
    public function __construct() {
        $_GET = $this->dataEncode($_GET);
        $_POST = $this->dataEncode($_POST);
        $_COOKIE = $this->dataEncode($_COOKIE);
        $_SERVER = $this->dataEncode($_SERVER);
    }
    protected function dataEncode($data){
        foreach ($data AS $k => $v){
            if(is_array($v)){
                unset($data[$k]);
                $data[htmlspecialchars($k)] = $this->dataEncode($v);
            }else{
                unset($data[$k]);
                $data[htmlspecialchars($k)] = htmlspecialchars($v);
            }
        }
        return $data;
    }
    protected function getDataByKey($data,$k,$def = null){
        if(isset($data[$k])){
            return $data[$k];
        }
        return $def;
    }

    public function get($k,$def = null){
        return $this->getDataByKey($_GET, $k, $def);
    }
    public function post($k,$def = null){
        return $this->getDataByKey($_POST, $k, $def);
    }
    public function session($k,$def = null){
        return $this->getDataByKey($_SESSION, $k, $def);
    }
    public function cookie($k,$def = null){
        return $this->getDataByKey($_COOKIE, $k, $def);
    }
    public function server($k,$def = null){
        return $this->getDataByKey($_SERVER, $k, $def);
    }
    public function cmd($k,$def = null){
        $arg = getopt('',["{$k}:"]);
        if(isset($arg[$k])){
            return htmlspecialchars($arg[$k]);
        }
        return $def;
    }
    /**
     * 注册API的数据
     * @param \g\app\callable $func
     */
    public function registerApiFunction(callable $func){
        $data = file_get_contents('php://input');
        $this->apiData = $this->dataEncode($func($data));
    }
    public function api($k,$def = null){
        if(isset($this->apiData[$k])){
            return $this->apiData[$k];
        }
        return null;
    }
}
