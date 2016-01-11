<?php
/**
 *	@author：耿鸿飞<eoe2005@qq.com>
 *	@date: 2016/1/11
 *	@Descript: 自动加载相关的数据
 */
define('DS',DIRECTORY_SEPARATOR);
define("G_DIR",realpath(dirname(__FILE__)).DS);

/**
 * 自动加载的类
 **/
function gautoloadclass($cls){
	$cls = preg_replace("/\\\/",DS,$cls);
	$file = G_DIR.$cls.'.php';
	if(file_exists($file)){
		include $file;
	}
}
spl_autoload_register('gautoloadclass');

