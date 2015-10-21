<?php

namespace Utils;

class Curl
{

    static $session = array();

    function Curl()
    {
        return $this;
    }

    function parseCookie($url, $content = '')
    {
        list($header, $body) = explode("\r\n\r\n", $content);
        preg_match("/set\-cookie:([^\r\n]*)/i", $header, $matches);

        $domain = parse_url($url);
        $domain = isset($domain['host']) ? $domain['host'] : '/';

        if (!empty($matches[1])) {
            $cookies = explode(';', $matches[1]);
            foreach ($cookies as $cookie) {
                $info = explode('=', $cookie);
                if (count($info) == 2) {
                    self::$session[trim($info[0])] = $info[1];
                }
            }
        }

        return $body;
    }

    function execute($method, $url, $fields = null, $userAgent = '', $httpHeaders = '', $username = '', $password = '')
    {
        $ch = Curl::create();
        if (false === $ch) {
            return false;
        }
        if (is_string($url) && strlen($url)) {
            $ret = curl_setopt($ch, CURLOPT_URL, $url);
        } else {
            return false;
        }

        //是否显示头部信息
        curl_setopt($ch, CURLOPT_HEADER, false);
        //
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        if ($username != '') {
            curl_setopt($ch, CURLOPT_USERPWD, $username . ':' . $password);
        }
        $method = strtolower($method);
        if ('post' == $method) {
            curl_setopt($ch, CURLOPT_POST, true);
            if (is_array($fields)) {
                $sets = array();
                foreach ($fields AS $key => $val) {
                    $sets[] = $key . '=' . urlencode($val);
                }
                $fields = implode('&', $sets);
            } elseif (is_string($fields)) {
                $fields = $fields;
            }
            curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        } else if ('put' == $method) {
            curl_setopt($ch, CURLOPT_PUT, true);
        }
        //curl_setopt($ch, CURLOPT_PROGRESS, true);
        //curl_setopt($ch, CURLOPT_VERBOSE, true);
        //curl_setopt($ch, CURLOPT_MUTE, false);
        //设置curl超时秒数
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        if (strlen($userAgent)) {
            curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);
        }
        if (is_array($httpHeaders)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $httpHeaders);
        }
        $ret = curl_exec($ch);
        if (curl_errno($ch)) {
            curl_close($ch);

            return false;
        } else {
            curl_close($ch);
            if (!is_string($ret)) {
                return false;
            }

            //当有头部返回时候,进行分割过滤.
            //$ret = $this->parseCookie($url, $ret);

            return $ret;

        }
    }

    function post($url, $fields, $userAgent = '', $httpHeaders = '', $username = '', $password = '')
    {
        $ret = Curl::execute('POST', $url, $fields, $userAgent, $httpHeaders, $username, $password);
        if (false === $ret) {
            return false;
        }
        if (is_array($ret)) {
            return false;
        }
        return $ret;
    }

    function get($url, $fields, $userAgent = '', $httpHeaders = '', $username = '', $password = '')
    {
        $ret = Curl::execute('GET', $url, $fields, $userAgent, $httpHeaders, $username, $password);
        if (false === $ret) {
            return false;
        }
        if (is_array($ret)) {
            return false;
        }
        return $ret;
    }

    function create()
    {
        $ch = null;
        if (!function_exists('curl_init')) {
            return false;
        }
        $ch = curl_init();
        if (!is_resource($ch)) {
            return false;
        }
        return $ch;
    }

}