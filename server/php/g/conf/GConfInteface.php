<?php
namespace g\conf;
/**
 * @author eoe2005@qq.com
 */
class GConfInteface {
    protected $data = [];
    public function get($k,$def = null){
        $ks = explode('.', $k);
        $ret = $this->data;
        foreach($ks AS $key){
            if(isset($ret[$k])){
                $ret = $ret[$key];
            }else{
                return $def;
            }
        }
        return $ret;
    }
    public function getInt($k,$def = 0){
        return intval($this->get($k,$def));
    }
    public function getString($k,$def = ''){
        return strval($this->get($k,$def));
    }
    public function getFloat($k,$def = 0.0){
        return floatval($this->get($k,$def));
    }
    public function getDouble($k,$def = 0.0){
        return $this->getFloat($k,$def);
    }
    public function getArray($k,$def = []){
        $r = $this->get($k);
        if($r){
            if(is_array($r)){
                return $r;
            }
        }
        return $def;
    }
}
