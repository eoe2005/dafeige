<?php

namespace g\pic;

/**
 * @author eoe2005@qq.com
 */
interface GPicInterface {
    const POSITION_BOTTOM_CENTER = 1;
    const POSITION_BOTTOM_LEFT = 2;
    const POSITION_BOTTOM_RIGHT = 3;
    const POSITION_TOP_CENTER = 4;
    const POSITION_TOP_LEFT = 5;
    const POSITION_TOP_RIGHT = 6;
    
    public function resize($src, $w, $h, $desc = null);
    public function resizePadding($src, $w, $h, $desc = null);
    public function water($src,$water,$pozition = slef::POSITION_BOTTOM_CENTER,$desc = null);
    public function waterText($src,$text,$position = self::POSITION_BOTTOM_RIGHT,$desc = null);
    public function clip($src,$w,$h,$desc = null);
    public function code($w,$h,$code);
}
