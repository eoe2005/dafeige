<?php

namespace g\net;

/**
 * @author eoe2005@qq.com
 */
class GMail {

    public static function ins($dns) {
        return new GSmtp($dns);
    }

}

class GMailBase {

    protected $subject = null;
    protected $msg = null;
    protected $from = '';
    protected $toMail = [];
    protected $ccMail = [];
    protected $bccMail = [];
    protected $tomails = [];
    protected $isHtml = false;
    protected $files = [];
    protected $charset = 'UTF-8';

    const CRCF = "\r\n";

    public function setTo($mails, $name = '') {
        if (empty($name)) {
            if (is_array($mails)) {
                foreach ($mails AS $mail => $name) {
                    if (is_integer($mail)) {
                        $mail = $name;
                        $this->tomails[] = $mail;
                        $name = strstr($mail, '@', true);
                        $this->toMail[] = sprintf('=?%s?B?%s?= <%s>', $this->charset, base64_encode($name), $mail);
                    } else {
                        $this->tomails[] = $mail;
                        $this->toMail[] = sprintf('=?%s?B?%s?= <%s>', $this->charset, base64_encode($name), $mail);
                    }
                }
            } else {
                $name = strstr($mails, '@', true);
                $this->tomails[] = $mails;
                $this->toMail[] = sprintf('=?%s?B?%s?= <%s>', $this->charset, base64_encode($name), $mails);
            }
        } else {
            $this->tomails[] = $mails;
            $this->toMail[] = sprintf('=?%s?B?%s?= <%s>', $this->charset, base64_encode($name), $mails);
        }
        return $this;
    }

    public function setCC($mails, $name = '') {
        if (empty($name)) {
            if (is_array($mails)) {
                foreach ($mails AS $mail => $name) {
                    if (is_integer($mail)) {
                        $mail = $name;
                        $name = strstr($mail, '@', true);
                        $this->ccMail[] = sprintf('=?%s?B?%s?= <%s>', $this->charset, base64_encode($name), $mail);
                    } else {
                        $this->ccMail[] = sprintf('=?%s?B?%s?= <%s>', $this->charset, base64_encode($name), $mail);
                    }
                }
            } else {
                $name = strstr($mails, '@', true);
                $this->ccMail[] = sprintf('=?%s?B?%s?= <%s>', $this->charset, base64_encode($name), $mails);
            }
        } else {
            $this->ccMail[] = sprintf('=?%s?B?%s?= <%s>', $this->charset, base64_encode($name), $mails);
        }
        return $this;
    }

    public function setBCC($mails, $name = '') {
        if (empty($name)) {
            if (is_array($mails)) {
                foreach ($mails AS $mail => $name) {
                    if (is_integer($mail)) {
                        $mail = $name;
                        $name = strstr($mail, '@', true);
                        $this->bccMail[] = sprintf('=?%s?B?%s?= <%s>', $this->charset, base64_encode($name), $mail);
                    } else {
                        $this->bccMail[] = sprintf('=?%s?B?%s?= <%s>', $this->charset, base64_encode($name), $mail);
                    }
                }
            } else {
                $name = strstr($mails, '@', true);
                $this->bccMail[] = sprintf('=?%s?B?%s?= <%s>', $this->charset, base64_encode($name), $mails);
            }
        } else {
            $this->bccMail[] = sprintf('=?%s?B?%s?= <%s>', $this->charset, base64_encode($name), $mails);
        }
        return $this;
    }

    public function addFile($filepath) {
        if (is_array($filepath)) {
            foreach ($filepath AS $path) {
                $this->files[] = $path;
            }
        } else {
            $this->files[] = $filepath;
        }
        return $this;
    }

    public function send($isHtml = false) {
        $this->isHtml = $this->isHtml = $isHtml;
        return $this->_send();
    }

    protected function _send() {
        
    }

    public function setSubject($title) {
        $this->subject = $title;
        return $this;
    }

    public function setMsg($msg) {
        $this->msg = $msg;
        return $this;
    }

    public function setFrom($mail, $name = null) {
        if (empty($name)) {
            $name = strstr($mail, '@', TRUE);
        }
        $this->from = sprintf('=?%s?B?%s?= <%s>', $this->charset, base64_encode($name), $mail);
        return $this;
    }

}

/**
 * 发送邮件的功能
 */
class GSmtp extends GMailBase {

    private $host;
    private $serverhost;
    private $username;
    private $timeout = 30;
    private $pwd;
    private $fd;

    public function __construct($dns) {
        $attr = explode(';', $dns);
        foreach ($attr AS $val) {
            $val = trim($val);
            $i = strpos($val, '=');
            if ($i > 0) {
                $name = strtolower(substr($val, 0, $i));
                switch ($name) {
                    case 'username':
                        $this->username = substr($val, $i + 1);
                        break;
                    case 'password':
                        $this->pwd = substr($val, $i + 1);
                        break;
                }
            } else {
                $this->serverhost = $val;
                $this->host = preg_replace("/(.+:\/\/)(.*)(:\d+)/", "$2", $val);
            }
        }
    }

    private function write($msg) {
        echo "\nSEND: " . $msg . self::CRCF;
        fwrite($this->fd, $msg . self::CRCF);
    }

    private function read($code) {
        $str = trim(fread($this->fd, 1024));
        echo 'R: ' . $str;
        $lines = explode("\r\n", $str);
        $len = count($lines);
        return strpos($lines[$len - 1], $code) === 0;
    }

    protected function _send() {
        $this->fd = stream_socket_client($this->serverhost, $errno, $errstr, $this->timeout);
        if ($this->read('220')) {
            $this->write(sprintf('HELO %s', $this->host));
            if ($this->read('250')) {
                $this->write('AUTH LOGIN');
                if ($this->read('334')) {
                    $this->write(base64_encode($this->username));
                    if ($this->read('334')) {
                        $this->write(base64_encode($this->pwd));
                        if ($this->read('235')) {
                            $this->write(sprintf('MAIL FROM:<%s>', $this->username));
                            if ($this->read('250')) {
                                foreach ($this->tomails AS $mail) {
                                    $this->write('RCPT TO:<' . $mail . '>');
                                    if (!$this->read('250')) {
                                        return false;
                                    }
                                }
                                $this->write('DATA');
                                if ($this->read('354')) {
                                    $this->write(sprintf('From: %s', $this->from));
                                    $this->write(sprintf('To: %s', implode(',', $this->toMail)));
                                    if (count($this->ccMail) > 0) {
                                        $this->write(sprintf('Cc: %s', implode(',', $this->ccMail)));
                                    }
                                    if (count($this->bccMail) > 0) {
                                        $this->write(sprintf('Bcc: %s', implode(',', $this->bccMail)));
                                    }
                                    $this->write(sprintf('Subject: =?utf-8?B?%s?=', base64_encode($this->subject)));
                                    $boundary = null;
                                    if (count($this->files) > 0) {
                                        $boundary = sprintf('----%s----', md5(time()));
                                    }
                                    if (is_null($boundary)) {
                                        if ($this->isHtml) {
                                            $this->write(sprintf('Content-Type: text/html;charset="%s"', $this->charset));
                                            $this->write('Content-Transfer-Encoding: base64');
                                            $this->write('');
                                            $this->write(base64_encode($this->msg));
                                        }else{
                                            $this->write('');
                                            $this->write($this->msg);
                                        }
                                    } else {
                                        $this->write(sprintf('Content-Type: multipart/mixed;boundary="%s"', $boundary));
                                        $this->write('');
                                        $this->write($this->msg);
                                        $this->write('');
                                        $this->write('--' . $boundary);
                                        
                                        $this->write(sprintf('Content-Type: text/html;charset="%s"', $this->charset));                                        
                                        $this->write('Content-Transfer-Encoding: base64');
                                        $this->write('');
                                        $this->write(base64_encode($this->msg));
                                        //$this->write($this->msg);    
                                        $this->write('');
                                        
                                        foreach ($this->files AS $f) {
                                            $this->write('--' . $boundary);
                                            $this->write(sprintf('Content-Type: application/octet-stream;name="%s"', basename($f)));
                                            $this->write('Content-Transfer-Encoding: base64');
                                            $this->write(sprintf('Content-Disposition: attachment;filename="%s"', basename($f)));
                                            $this->write('');
                                            $this->write(base64_encode(file_get_contents($f)));
                                        }
                                        
                                        $this->write($boundary . '--');
                                    }

                                    $this->write('.');
                                    if ($this->read('250')) {
                                        $this->write('QUIT');
                                        fclose($this->fd);
                                        return TRUE;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        fclose($this->fd);
        return FALSE;
    }

}
