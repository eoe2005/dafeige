<?php

namespace g\net;

/**
 * @author eoe2005@qq.com
 */
class GHttpClient {

    public function __construct() {
        ;
    }

    /**
     * GET 请求
     * @param type $url
     * @param type $args
     * @param type $header
     * @return \g\net\GHttpResponse
     */
    public function get($url, $args = [], $header = []) {
        if (strpos($url, '?') !== FALSE) {
            $url .= http_build_query($args);
        } else {
            $url .= '?' . http_build_query($args);
        }
        return $this->curlSend($url, [CURLOPT_HTTPHEADER => $header]);
    }
    
    /**
     * POST 请求
     * @param type $url
     * @param type $args
     * @param type $header
     * @return \g\net\GHttpResponse
     */
    public function post($url, $args, $header = []) {
        return $this->curlSend($url, [
            CURLOPT_POST => TRUE,
            CURLOPT_POSTFIELDS => $args,
            CURLOPT_HTTPHEADER => $header]);
    }
    
    /**
     * HEAD 请求
     * @param type $url
     * @param type $args
     * @param type $header
     * @return \g\net\GHttpResponse
     */
    public function head($url, $args = [], $header = []) {
        if (strpos($url, '?') !== FALSE) {
            $url .= http_build_query($args);
        } else {
            $url .= '?' . http_build_query($args);
        }
        return $this->curlSend($url, [CURLOPT_HTTPHEADER => $header,CURLOPT_CUSTOMREQUEST => 'HEAD']);
    }

    /**
     * PUT 请求
     * @param type $url
     * @param type $args
     * @param type $header
     * @return \g\net\GHttpResponse
     */
    public function put($url, $args, $header = []) {
        return $this->curlSend($url, [
            CURLOPT_CUSTOMREQUEST => 'PUT',
            CURLOPT_POSTFIELDS => $args,
            CURLOPT_HTTPHEADER => $header]);
    }

    /**
     * DELETE 请求
     * @param type $url
     * @param type $args
     * @param type $header
     * @return \g\net\GHttpResponse
     */
    public function delete($url, $args = [], $header = []) {
        if (strpos($url, '?') !== FALSE) {
            $url .= http_build_query($args);
        } else {
            $url .= '?' . http_build_query($args);
        }
        return $this->curlSend($url, [CURLOPT_HTTPHEADER => $header,CURLOPT_CUSTOMREQUEST => 'DELETE',]);
    }

    /**
     * OPTIONS 请求
     * @param type $url
     * @param type $args
     * @param type $header
     * @return \g\net\GHttpResponse
     */
    public function options($url, $args = [], $header = []) {
        if (strpos($url, '?') !== FALSE) {
            $url .= http_build_query($args);
        } else {
            $url .= '?' . http_build_query($args);
        }
        return $this->curlSend($url, [CURLOPT_HTTPHEADER => $header,CURLOPT_CUSTOMREQUEST => 'OPTIONS']);
    }

    /**
     * TRACE 请求
     * @param type $url
     * @param type $args
     * @param type $header
     * @return \g\net\GHttpResponse
     */
    public function trace($url, $args = [], $header = []) {
        if (strpos($url, '?') !== FALSE) {
            $url .= http_build_query($args);
        } else {
            $url .= '?' . http_build_query($args);
        }
        return $this->curlSend($url, [CURLOPT_HTTPHEADER => $header,CURLOPT_CUSTOMREQUEST => 'TREACE']);
    }

    /**
     * 向服务器发送数据
     * @param type $url
     * @param type $options
     * @return \g\net\GHttpResponse
     */
    protected function curlSend($url, $options) {
        $ch = curl_init($url);
        $options[CURLOPT_HEADER] = true;
        $options[CURLOPT_RETURNTRANSFER] = true;
        if (isset($options[CURLOPT_HTTPHEADER])) {
            $options[CURLOPT_HTTPHEADER][] = 'Content-Type:text/xml;charset=UTF-8';
        } else {
            $options[CURLOPT_HTTPHEADER] = ['Content-Type:text/xml;charset=UTF-8'];
        }
        curl_setopt_array($ch, $options);
        $rs = curl_exec($ch);
        $info = curl_getinfo($ch);
        $error = curl_errno($ch);
        $errorMsg = curl_error($ch);
        curl_close($ch);
        return new GHttpResponse($info, $error, $errorMsg, $rs);
    }

}

class GHttpResponse {

    protected $data = ['head' => []];

    public function __construct($info, $errorNo, $errorMsg, $res) {
        $this->data['errorNo'] = $errorNo;
        $this->data['errorMsg'] = $errorMsg;
        $this->data['info'] = $info;
        if ($errorNo) {
            
        } else {
            $index = strpos($res, "\r\n\r\n");
            if ($index) {
                $head = substr($res, 0, $index);
                $this->data['body'] = substr($res, $index + 4);
                $heads = explode("\r\n", $head);
                foreach ($heads AS $line) {
                    if (substr($line, 0, 4) == 'HTTP') {
                        $rcode = explode(" ", $line);
                        $this->data['code'] = $rcode[1];
                    } else {
                        $kv = explode(": ", $line);
                        if (isset($this->data['head'][$kv[0]])) {
                            if (is_array($this->data['head'][$kv[0]])) {
                                $this->data['head'][$kv[0]][] = $kv[1];
                            } else {
                                $this->data['head'][$kv[0]] = [$this->data['head'][$kv[0]]];
                                $this->data['head'][$kv[0]][] = $kv[1];
                            }
                        } else {
                            $this->data['head'][$kv[0]] = $kv[1];
                        }
                    }
                }
            } else {
                throw new \g\exception\GException("返回结果错误");
            }
        }
    }

    public function getErrorNo() {
        if (isset($this->data['errorNo'])) {
            return $this->data['errorNo'];
        }
        return null;
    }

    public function getErrorMsg() {
        if (isset($this->data['errorMsg'])) {
            return $this->data['errorMsg'];
        }
        return null;
    }

    public function getInfo() {
        if (isset($this->data['info'])) {
            return $this->data['info'];
        }
        return null;
    }

    /**
     * 获取返回结果中head中的摸一个数据
     * @param String $key
     * @return type
     */
    public function getHead($key) {
        if (isset($this->data['head'][$key])) {
            return $this->data['head'][$key];
        }
        return null;
    }

    /**
     * 获取返回CODE
     * @return Int
     */
    public function getCode() {
        if (isset($this->data['info']['http_code'])) {
            return $this->data['info']['http_code'];
        } elseif (isset($this->data['code'])) {
            return $this->data['code'];
        }
        return null;
    }

    /**
     * 获取返回的数据
     * @return type
     */
    public function getData() {
        if (isset($this->data['body'])) {
            return $this->data['body'];
        }
        return null;
    }

    /**
     * 获取返回的XML
     * @return SimpleXMLElement
     */
    public function getDataXML() {
        $data = $this->getData();
        if ($data) {
            return simplexml_load_string($data);
        }
        return null;
    }

    /**
     * 获取返回的JSON
     * @return Array|Object
     */
    public function getDataJSON($isReturnOnject = false) {
        $data = $this->getData();
        if ($data) {
            return json_decode($data, $isReturnOnject);
        }
        return null;
    }

}
