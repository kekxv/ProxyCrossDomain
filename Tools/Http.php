<?php
/**
 * Created by PhpStorm.
 * User: Caesar
 * Date: 2018/9/25
 * Time: 15:48
 *
 * 工具类
 */

namespace Tools;

class Http
{
    const UserAgent = "Mozilla/5.0 (iPhone; CPU iPhone OS 11_0 like Mac OS X) AppleWebKit/604.1.38 (KHTML, like Gecko) Version/11.0 Mobile/15A372 Safari/604.1";
    /**
     * 发送HTTP请求
     *
     * @param string $url 请求地址
     * @param mixed $data 发送数据
     * @param string $refererUrl 请求来源地址
     * @param string $method 请求方式 GET/POST
     * @param string $contentType
     * @param $Headers
     * @param int $timeout
     * @param bool $proxy
     * @param bool $debug
     * @return boolean|mixed
     */
    public static function Send($url, $data, $refererUrl = '', $method = 'GET', $contentType = 'application/json', $Headers = null, $timeout = 30, $proxy = false, $debug = false)
    {
        $ch = null;
        if ($Headers == null)
            $Headers = [];
        $Headers[] = 'User-Agent:' . self::UserAgent;
//            $Headers = ["User-Agent" => "Mozilla/5.0 (iPhone; CPU iPhone OS 11_0 like Mac OS X) AppleWebKit/604.1.38 (KHTML, like Gecko) Version/11.0 Mobile/15A372 Safari/604.1"];
        if ('POST' === strtoupper($method)) {
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_USERAGENT,self::UserAgent);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_FRESH_CONNECT, 1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
            curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
            if ($refererUrl) {
                curl_setopt($ch, CURLOPT_REFERER, $refererUrl);
            }
            if ($contentType) {
                $Headers[] = 'Content-Type:' . $contentType;
            }
            if (count($Headers) > 0)
                curl_setopt($ch, CURLOPT_HTTPHEADER, $Headers);
            if (is_string($data)) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            } else {
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
            }
        } else if ('GET' === strtoupper($method)) {
            if (is_string($data)) {
                $real_url = $url . (strpos($url, '?') === false ? '?' : '') . $data;
            } else {
                $real_url = $url . (strpos($url, '?') === false ? '?' : '') . http_build_query($data);
            }

            $ch = curl_init($real_url);
            curl_setopt($ch, CURLOPT_USERAGENT,self::UserAgent);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            if ($contentType) {
                $Headers[] = 'Content-Type:' . $contentType;
            }
            if (count($Headers) > 0)
                curl_setopt($ch, CURLOPT_HTTPHEADER, $Headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
            if ($refererUrl) {
                curl_setopt($ch, CURLOPT_REFERER, $refererUrl);
            }
        } else {
            $args = func_get_args();
            return false;
        }

        if ($proxy) {
            curl_setopt($ch, CURLOPT_PROXY, $proxy);
        }
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 跳过证书检查
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);  // 从证书中检查SSL加密算法是否存在
        if (!$result = curl_exec($ch)) {
            if ($debug) var_dump(curl_error($ch));
        }
        $info = curl_getinfo($ch);
        $contents = array(
            'httpInfo' => array(
                'send' => $data,
                'url' => $url,
                'ret' => $result,
                'http' => $info,
            )
        );
        curl_close($ch);
        return $result;
    }
}

