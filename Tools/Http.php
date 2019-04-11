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
    const PcUserAgent = "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_4) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/73.0.3683.103 Safari/537.36";

    /**
     * 发送HTTP请求
     *
     * @param $option
     * @return boolean|mixed
     */
    public static function Send($option)
    {
        if (!is_array($option)) return false;

        $url = $option["url"];
        $data = $option["data"];
        $refererUrl = $option["refererUrl"] ?? '';
        $method = $option["method"] ?? 'GET';
        $contentType = $option["contentType"] ?? 'application/json';
        $Headers = $option["Headers"] ?? null;
        $timeout = $option["timeout"] ?? 30;
        $isPc = $option["isPc"] ?? false;
        $proxy = $option["proxy"] ?? false;
        $debug = $option["debug"] ?? false;

        $ch = null;
        if ($Headers == null)
            $Headers = [];
        $Headers[] = 'User-Agent:' . ($isPc ? self::PcUserAgent : self::UserAgent);
        //            $Headers = ["User-Agent" => "Mozilla/5.0 (iPhone; CPU iPhone OS 11_0 like Mac OS X) AppleWebKit/604.1.38 (KHTML, like Gecko) Version/11.0 Mobile/15A372 Safari/604.1"];
        if ('POST' === strtoupper($method)) {
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_USERAGENT, self::UserAgent);
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
            curl_setopt($ch, CURLOPT_USERAGENT, self::UserAgent);
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

