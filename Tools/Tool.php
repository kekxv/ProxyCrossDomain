<?php
/**
 * 工具类
 *
 * Created by PhpStorm.
 * User: Caesar
 * Date: 2018/9/21
 * Time: 13:57
 */

namespace Tools;

class Tool
{
    /**
     * 判断是否是 支付宝微信
     * @return int 1 微信 2 支付宝 0 一般浏览器
     */
    public static function IsWeChatOrAlipay()
    {
        //判断是不是微信
        if (stripos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) {
            return 1;
        }
        //判断是不是支付宝
        if (stripos($_SERVER['HTTP_USER_AGENT'], 'AlipayClient') !== false) {
            return 2;
        }
        //哪个都不是
        return 0;
    }

    /**
     * 转码
     * @param $str
     * @param string $mode
     * @return string 转码后字符串
     */
    public static function ConvertString($str, $mode = 'UTF-8')
    {
        $encode = mb_detect_encoding($str, array("ASCII", 'UTF-8', "GB2312", "GBK", 'BIG5'));
        return mb_convert_encoding($str, $mode, $encode);
    }

    /**
     * 是否是AJAx提交的
     * @return bool
     */
    public static function isAjax()
    {
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 是否是GET提交的
     */
    public static function isGet()
    {
        return $_SERVER['REQUEST_METHOD'] == 'GET' ? true : false;
    }

    /**
     * 是否是POST提交
     * @return int
     */
    public static function isPost()
    {
        return ($_SERVER['REQUEST_METHOD'] == 'POST' || (empty($_SERVER['HTTP_REFERER']) || preg_replace("~https?:\/\/([^\:\/]+).*~i", "\\1", $_SERVER['HTTP_REFERER']) == preg_replace("~([^\:]+).*~", "\\1", $_SERVER['HTTP_HOST']))) ? 1 : 0;
    }

}
