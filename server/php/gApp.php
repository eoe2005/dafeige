<?php

/**
 * 	@author：耿鸿飞<eoe2005@qq.com>
 * 	@date: 2016/1/11
 * 	@Descript: 自动加载相关的数据
 */
defined('DS') || define('DS', DIRECTORY_SEPARATOR);
define("G_DIR", realpath(dirname(__FILE__)) . DS);

/**
 * 自动加载的类
 * */
class GAutoLoadClass {

    protected static $_AUTOLOADPATH = [];

    /**
     * 添加自动加载类的路径
     * @param type $paths
     */
    public static function addPaths($paths) {
        if (is_array($paths)) {
            foreach ($paths AS $path) {
                $len = strlen($path);
                if (substr($path, $len - 2) != '/') {
                    $path .= DS;
                }
                if (isset(self::$_AUTOLOADPATH[$path]) === false) {
                    self::$_AUTOLOADPATH[$path] = $path;
                }
            }
        } else {
            $len = strlen($paths);
            if (substr($paths, $len - 2) != '/') {
                $paths .= DS;
            }
            if (isset(self::$_AUTOLOADPATH[$paths]) === false) {
                self::$_AUTOLOADPATH[$paths] = $paths;
            }
        }
    }

    public static function loadClass($cls) {
        if ($cls == 'GLog') {
            return include G_DIR . 'g' . DS . 'log' . DS . 'GLog.php';
        } else {
            $cls = preg_replace("/\\\/", DS, $cls);
            $file = G_DIR . $cls . '.php';
            if (file_exists($file)) {
                include $file;
            }else{
                foreach (self::$_AUTOLOADPATH AS $path){
                    $file = $path.$cls.'.php';
                     //var_dump($cls,self::$_AUTOLOADPATH,$file);
                    if(file_exists($file)){
                        return include $file;
                    }
                }
            }            
        }
       
    }

}

spl_autoload_register('GAutoLoadClass::loadClass');

