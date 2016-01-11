<?php
namespace g\conf;
/**
 * @author eoe2005@qq.com
 */
class GConfXml  extends GConfInteface{
    public function __construct($file) {
        if(file_exists($file)){
            $xml = simplexml_load_file($file);
            $this->data = $this->xmltoArray($xml);
            //var_dump($this->data);
        }else{
            throw new \g\exception\GException($file.'配置文件不存在!');
        }
    }
    protected function xmltoArray($xml){
        $rs = [];
        foreach ($xml->children() AS $item){
            $name = $item->getName();
            if($item->count() == 0){                
                $attr = $item->attributes();
                if(count($attr) == 0){
                    $rs[$name] = strval($item);
                }else{
                    
                }
            }else{
                $rs[$name] = $this->xmltoArray($item);
            }
        }
        return $rs;
    }
}
