<?php

namespace g\pic;

/**
 * @author eoe2005@qq.com
 */
class GPicGD implements GPicInterface {

    /**
     * 截取突破一部分
     * @param type $src
     * @param type $w
     * @param type $h
     * @param type $desc
     * @return boolean
     */
    public function clip($src, $w, $h, $desc = null) {
        $info = $this->imgInfo($src);
        $out = imagecreatetruecolor($w, $h);
        $back = imagecolorallocatealpha($out, 255, 255, 255, 0);
        imagefilledrectangle($out, 0, 0, $w, $h, $back);
        $x = 0;
        $y = 0;
        $cw = $info['w'];
        $ch = $info['h'];
        $sf = $info['w'] / $info['h'];
        $of = $w / $h;
        if ($sf > $of) {
            $cw = $ch * $of;
            $x = abs($info['w'] - $cw) / 2;
        } else {
            $ch = $cw / $of;
            $y = abs($info['h'] - $ch) / 2;
        }
        imagecopyresampled($out, $info['res'], 0, 0, $x, $y, $w, $h, $cw, $ch);
        imagedestroy($info['res']);
        if ($desc) {
            imagepng($out, $desc);
        } else {
            header("Content-type: image/png");
            imagepng($out);
        }
        imagedestroy($out);
        return true;
    }

    public function code($width, $height, $code) {
        //ob_start();
        $img = imagecreatetruecolor($width, $height);
        $fontname = dirname(__FILE__) . DS . 'fangsong.ttf';
        //imagelayereffect($img, IMG_EFFECT_OVERLAY);
        $back = imagecolorallocatealpha($img, 255, 255, 255, 0);
        imagefilledrectangle($img, 0, 0, $width, $height, $back);
        $fontcolor = imagecolorallocate($img, 44, 30, 115);
        $x = 0;
        $y = 0;
        $w = $width * 0.8;
        $h = $height * 0.8;
        $fsize = min($w, $h);
        $box = $this->getFontSize($w, $h, $fsize, $fontname, $code);

        $x = ($width - $box[0]) / 2;
        $y = ($height) / 2 + $box[1] / 2 - 5; // - ($height - $box[1])/2;
        //var_dump($img, $fsize, 0, $x, $y, $fontcolor, $fontname, $code);
        imagefttext($img, $fsize, 0, $x, $y, $fontcolor, $fontname, $code);
        //ob_end_clean();
        header("Content-type: image/png");
        imagepng($img);
        imagedestroy($img);
    }

    /**
     * 修改图片大小
     * @param type $src
     * @param type $w
     * @param type $h
     * @param type $desc
     * @return boolean
     */
    public function resize($src, $w, $h, $desc = null) {
        //var_dump($src);die();
        $info = $this->imgInfo($src);
        $out = imagecreatetruecolor($w, $h);
        imagecopyresampled($out, $info['res'], 0, 0, 0, 0, $w, $h, $info['w'], $info['h']);
        imagedestroy($info['res']);
        if ($desc) {
            imagepng($out, $desc);
        } else {
            header("Content-type: image/png");
            imagepng($out);
        }
        imagedestroy($out);
        return true;
    }

    /**
     * 有边框的设置图片大小
     * @param type $src
     * @param type $w
     * @param type $h
     * @param type $desc
     * @return boolean
     */
    public function resizePadding($src, $w, $h, $desc = null) {
        $info = $this->imgInfo($src);
        $out = imagecreatetruecolor($w, $h);
        $back = imagecolorallocate($out, 255, 255, 255);
        imagefilledrectangle($out, 0, 0, $w, $h, $back);
        $x = 0;
        $y = 0;
        $cw = $w;
        $ch = $h;
        $sf = $info['w'] / $info['h'];
        $of = $w / $h;
        if ($sf > $of) {
            $ch = $cw / $sf;
            $y = abs($h - $ch) / 2;
        } else {
            $cw = $ch * $sf;
            $x = abs($w - $cw) / 2;
        }
        imagecopyresampled($out, $info['res'], $x, $y, 0, 0, $cw, $ch, $info['w'], $info['h']);
        imagedestroy($info['res']);
        if ($desc) {
            imagepng($out, $desc);
        } else {
            header("Content-type: image/png");
            imagepng($out);
        }
        imagedestroy($out);
        return true;
    }

    public function water($src, $water, $pozition = 1, $desc = null) {
        $info = $this->imgInfo($src);
        $winfo = $this->imgInfo($water);
        $fontname = dirname(__FILE__) . DS . 'fangsong.ttf';
        $x = 0;
        $y = 0;
        $w = $winfo['w'];
        $h = $winfo['h'];
        $f = $winfo['w'] / $winfo['h'];
        if ($w > $info['w']) {
            $w = $info['w'] * 0.75;
            $h = $w / $f;
        }
        if ($h > $info['h']) {
            $h = $info['h'] * 0.75;
            $w = $f * $h;
        }
        switch ($position) {
            case self::POSITION_TOP_CENTER:
                $x = ($info['w'] - $w) / 2;
                $y = 5;
                break;
            case self::POSITION_TOP_LEFT:
                $x = 5;
                $y = 5;
                break;
            case self::POSITION_TOP_RIGHT:
                $x = $info['w'] - $w - 5;
                $y = 5;
                break;
            case self::POSITION_BOTTOM_LEFT:
                $x = 5;
                $y = $info['h'] - $h - 5;
                break;
            case self::POSITION_BOTTOM_RIGHT:
                $x = $info['w'] - $w - 5;
                $y = $info['h'] - $h - 5;
                break;
            default :
                $x = ($info['w'] - $w) / 2;
                $y = $info['h'] - $h - 5;
                break;
        }
        imagecopyresampled($info['res'], $winfo['res'], $y, $x, 0, 0, $w, $h, $winfo['w'], $winfo['h']);
        if ($desc) {
            imagepng($info['res'], $desc);
        } else {
            header("Content-type: image/png");
            imagepng($info['res']);
        }
        imagedestroy($winfo['res']);
        imagedestroy($info['res']);
        return true;
    }

    public function waterText($src, $text, $position = 1, $desc = null) {
        $info = $this->imgInfo($src);
        $fontname = dirname(__FILE__) . DS . 'fangsong.ttf';
        $fontcolor = imagecolorallocate($info['res'], 255, 255, 255);
        $x = 0;
        $y = 0;
        $w = $info['w'] * 0.75;
        $h = $info['h'] * 0.75;
        $fsize = min($w, $h);
        $box = $this->getFontSize($w, $h, $fsize, $fontname, $text);
        switch ($position) {

            case self::POSITION_TOP_CENTER:
                $x = ($info['w'] - $box[0]) / 2;
                $y = $box[1] + 5;
                break;
            case self::POSITION_TOP_LEFT:
                $x = $box[1] / 2;
                $y = $box[1] + 5;
                break;
            case self::POSITION_TOP_RIGHT:
                $x = $info['w'] - $box[0] - $box[1] / 2;
                $y = $box[1] + 5;
                break;
            case self::POSITION_BOTTOM_LEFT:
                $x = $box[1] / 2;
                $y = $info['h'] - $box[1] / 2;
                break;
            case self::POSITION_BOTTOM_RIGHT:
                $x = $info['w'] - $box[0] - $box[1] / 2;
                $y = $info['h'] - $box[1] / 2;
                break;
            default :
                $x = ($info['w'] - $box[0]) / 2;
                $y = $info['h'] - $box[1] / 2;
                break;
        }
        imagefttext($info['res'], $fsize, 0, $x, $y, $fontcolor, $fontname, $text);
        if ($desc) {
            imagepng($info['res'], $desc);
        } else {
            header("Content-type: image/png");
            imagepng($info['res']);
        }
        imagedestroy($info['res']);
        return true;
    }

    private function imgInfo($src) {
        $info = getimagesize($src);
        if ($info === FALSE) {
            throw new \g\exception\GException($src . "不是图片");
        }
        $ret = ['w' => $info[0], 'h' => $info[1]];
        switch ($info[2]) {
            case 3:
                $ret['res'] = imagecreatefrompng($src);
                break;
            case 1:
                $ret['res'] = imagecreatefromgif($src);
                break;
            case 2:
                $ret['res'] = imagecreatefromjpeg($src);
                break;
            default :
                //var_dump($info);
                throw new \g\exception\GException($src . "不是支持的图片");
                ;
        }
        return $ret;
    }

    protected function getFontSize($w, $h, &$size, $fontfile, $text, $angle = 0) {
        $info = imageftbbox($size, $angle, $fontfile, $text);
        $x = [];
        $y = [];
        foreach ($info AS $k => $v) {
            if ($k % 2 == 0) {
                $x[] = $v;
            } else {
                $y[] = $v;
            }
        }
        $fw = max($x) - min($x);
        $fh = max($y) - min($y);
        if ($w >= $fw AND $h >= $fh) {
            return [$fw, $fh];
        }
        $min = min($w, $h, $fw, $fh);
        if ($min < $size) {
            $size = $min;
            return $this->getFontSize($w, $h, $size, $fontfile, $text, $angle);
        } else {
            $size -= 0.5;
            return $this->getFontSize($w, $h, $size, $fontfile, $text, $angle);
        }
    }

}
