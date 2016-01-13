<?php

/**
 * @author eoe2005@qq.com
 */
class GLog {

    /**
     * 要保存到文件的位置
     * @var type 
     */
    protected static $LogPath = false;
    /**
     * 是否按照日期分割目录
     * @var type 
     */
    protected static $LogSplitDir = false;
    /**
     * 是否支持多文件
     * @var type 
     */
    protected static $LogMulitFile = false;

    public static function config($pathdir = null,$useMulitFile = FALSE, $splitDir = False) {
        if ($pathdir) {
            if (substr($pathdir, -1) != '/') {
                $pathdir .= '/';
            }
            if (is_dir($pathdir)) {
                if (is_writable($pathdir)) {
                    self::$LogPath = $pathdir;
                    self::$LogSplitDir = $splitDir;
                    self::$LogMulitFile = $useMulitFile;
                } else {
                    throw new g\exception\GException($pathdir . '没有写权限');
                }
            } else {
                throw new g\exception\GException($pathdir . '目录不存在');
            }
        }
    }

    public static function info() {
        self::log(__FUNCTION__, func_get_args());
    }

    public static function error() {
        self::log(__FUNCTION__, func_get_args());
    }

    public static function debug() {
        self::log(__FUNCTION__, func_get_args());
    }
    public static function __callStatic($name, $arguments) {
        self::log($name, $arguments);
    }

    protected static function log($name,$args){
        foreach ($args AS $k => $v){
            if(is_array($v) OR is_object($v)){
                $args[$k] = json_encode($v);
            }
        }
        if(self::$LogPath){
            $dir = "";
            $data = '';
            if(self::$LogSplitDir){
                $dir = date("Y/m/");
            }
            if(self::$LogMulitFile){
                $dir .= $name.'.log';
                $data = sprintf("[%s]\t%s\n",date('Y-m-d H:i:s'),  implode("\t", $args));
            }else{
                $dir .= 'info.log';
                $data = sprintf("[%s]:%s %s\n",date('Y-m-d H:i:s'),$name,  implode("\t", $args));
            }
            self::saveFile($dir, $data);
        }else{
            echo sprintf("[%s]:%s %s\n",date('Y-m-d H:i:s'),$name,  implode("\t", $args));
        }
    }
    protected static function saveFile($filename,$data){
        $path = self::$LogPath.$filename;
        $dir = dirname($path);
        if(file_exists($dir) === FALSE){
            @mkdir($dir, 0777, true);
        }
        file_put_contents($path, $data,FILE_APPEND);
    }
    

}
