<?php

namespace g\conf;

/**
 * @author eoe2005@qq.com
 */
class GConfIni extends GConfInteface {

    public function __construct($file) {
        if (file_exists($file)) {
            $data = parse_ini_file($file, TRUE);
            $this->setData($data);
        } else {
            throw new \g\exception\GException($file . '配置文件不存在!');
        }
    }

    protected function setData($data) {
        $this->data = $this->setDataByArray($data);       
    }

    protected function setDataByArray($data) {
        $ret = [];
        foreach ($data AS $k => $v) {
            if (preg_match("/\./", $k)) {
                $keys = explode('.', $k);
                $this->setKs($ret, $keys, $v);
            } else {
                $ret[$k] = $v;
            }
        }
        return $ret;
    }
    protected function setKs(&$data,$keys,$val){
        $k = array_shift($keys);
        if(isset($data[$k]) === false){
            if(count($keys) == 0){
                $data[$k] = $val;                
            }else{
                $data[$k] = [];
                $this->setKs($data[$k],$keys,$val);
            }
        }else{
            if(count($keys) === 0 ){
                $data[$k] = $val;
            }else{
                $this->setKs($data[$k],$keys,$val);
            }
        }
    }

}
