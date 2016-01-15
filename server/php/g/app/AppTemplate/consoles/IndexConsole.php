<?php

/**
 * @author eoe2005@qq.com
 */
class IndexConsole extends g\app\GAppConsoleController{
    public function indexCmd(){
        $a = $this->input->cmd("test");
        echo '这是一个测试参数 test  :'.$a ."\n";
    }
}
