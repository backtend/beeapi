<?php
declare (strict_types=1);

namespace app\common\helper;


class NetworkHelper
{


    /**
     * 私有拒绝构造
     */
    private function __construct()
    {
    }

    /**
     * 私有拒绝克隆
     */
    private function __clone()
    {
    }

    /**
     * 判断是否异步请求
     * @return boolean
     */
    public static function isAjax()
    {
        //Request::initial()->is_ajax()
        if (isset($_SERVER["HTTP_X_REQUESTED_WITH"]) && strtolower($_SERVER["HTTP_X_REQUESTED_WITH"]) == "xmlhttprequest") {
            return TRUE;
        }
        return false;
    }

    /**
     * 获取客户端ip
     * @param unknown $str
     * @return string
     */
    public static function ip()
    {
        //建议使用Request::$client_ip
        $ip = '';
        if (getenv("HTTP_CLIENT_IP"))
            $ip = getenv("HTTP_CLIENT_IP");
        else if (getenv("HTTP_X_FORWARDED_FOR"))
            $ip = getenv("HTTP_X_FORWARDED_FOR");
        else if (getenv("REMOTE_ADDR"))
            $ip = getenv("REMOTE_ADDR");
        return $ip;
    }


    /**
     * 获取客户端浏览器代理agent
     * @param string $str useless
     * @return unknown
     */
    public static function ua($type = 'normal')
    {
        //建议使用：Request::$user_agent 或Request::user_agent(['platform','mobile'])
        //name-version
        if (empty($_SERVER['HTTP_USER_AGENT'])) {
            return 'UserAgentNull';//当浏览器没有发送访问者的信息的时候
        }
        $agent = $_SERVER['HTTP_USER_AGENT'];
        $name = $vers = '';
        if (preg_match('/MSIE\s(\d+)\..*/i', $agent, $regs)) {
            $name = 'IE';
            $vers = isset($regs[1]) ? $regs[1] : '';
        } elseif (preg_match('/FireFox\/(\d+)\..*/i', $agent, $regs)) {
            $name = 'FireFox';
            $vers = isset($regs[1]) ? $regs[1] : '';
        } elseif (preg_match('/Opera[\s|\/](\d+)\..*/i', $agent, $regs)) {
            $name = 'Opera';
            $vers = isset($regs[1]) ? $regs[1] : '';
        } elseif (preg_match('/Chrome\/(\d+)\..*/i', $agent, $regs)) {
            $name = 'Chrome';
            $vers = isset($regs[1]) ? $regs[1] : '';
        } elseif ((strpos($agent, 'Chrome') == false) && preg_match('/Safari\/(\d+)\..*$/i', $agent, $regs)) {
            $name = 'Safari';
            $vers = isset($regs[1]) ? $regs[1] : '';
        }
        $name = $name === '' ? 'x' : $name;
        $vers = $vers === '' ? '0' : $vers;

        if ($name === 'x') {
            $bot = false;//if agent is spider
            $ua = addslashes(strtolower($_SERVER['HTTP_USER_AGENT']));
            if (strpos($ua, 'googlebot') !== false) {
                $bot = 'Google';
            } elseif (strpos($ua, 'mediapartners-google') !== false) {
                $bot = 'Google Adsense';
            } elseif (strpos($ua, 'baiduspider') !== false) {
                $bot = 'Baidu';
            } elseif (strpos($ua, 'sogou spider') !== false) {
                $bot = 'Sogou';
            } elseif (strpos($ua, 'sogou web') !== false) {
                $bot = 'Sogou web';
            } elseif (strpos($ua, 'sosospider') !== false) {
                $bot = 'SOSO';
            } elseif (strpos($ua, '360spider') !== false) {
                $bot = '360Spider';
            } elseif (strpos($ua, 'yahoo') !== false) {
                $bot = 'Yahoo';
            } elseif (strpos($ua, 'msn') !== false) {
                $bot = 'MSN';
            } elseif (strpos($ua, 'msnbot') !== false) {
                $bot = 'msnbot';
            } elseif (strpos($ua, 'sohu') !== false) {
                $bot = 'Sohu';
            } elseif (strpos($ua, 'yodaoBot') !== false) {
                $bot = 'Yodao';
            } elseif (strpos($ua, 'twiceler') !== false) {
                $bot = 'Twiceler';
            } elseif (strpos($ua, 'ia_archiver') !== false) {
                $bot = 'Alexa_';
            } elseif (strpos($ua, 'iaarchiver') !== false) {
                $bot = 'Alexa';
            } elseif (strpos($ua, 'yisouspider') !== false) {
                $bot = 'Yisou';
            } elseif (strpos($ua, 'jikespider') !== false) {
                $bot = 'Jike';
            } elseif (strpos($ua, 'bingbot') !== false) {
                $bot = 'Bingbot';
            } elseif (strpos($ua, 'slurp') !== false) {
                $bot = 'Yahoo slurp';
            } elseif (strpos($ua, 'bot') !== false) {
                $bot = 'otRobot';
            } elseif (strpos($ua, 'spider') !== false) {
                $bot = 'otSpider';
            }
            if ($bot !== false) {
                return 'Spider:' . $bot;
            }
        }

        return $name . ' ' . $vers;
    }


    /**
     * 获取设备类型
     * web/wap/weixin/alipay etc...
     * @return string
     */
    public static function device()
    {
        //建议组合使用：Request::user_agent(['mobile'])
        $agent = Arr::get($_SERVER, 'HTTP_USER_AGENT');
        if (preg_match('/Mobile/i', $agent) and preg_match('/MicroMessenger/i', $agent)) {
            return 'weixin';
        }
        if (preg_match('/AlipayClient/i', $agent)) {
            return "alipay";
        }
        if (preg_match('/Mobile/i', $agent)) {
            return 'wap';
        }
        return 'web';
    }


    /**
     * 发起一个http请求
     */
    public static function curl($url, $method = 'GET', array $param = [], $options = [])
    {
        $ch = curl_init();//句柄
        if ($method == 'POST') {
            curl_setopt($ch, CURLOPT_POST, 1);//post提交方式
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($param, JSON_UNESCAPED_UNICODE));
        } else {
            $url .= '?' . http_build_query($param);
        }

        //配置是json的payload提交形式
        if (isset($options['json']) and $options['json'] === true) {
            $header = [
                'Content-Type: application/json; charset=utf-8',
                'Content-Length:' . strlen(json_encode($param, JSON_UNESCAPED_UNICODE))
            ];
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        } else {
            curl_setopt($ch, CURLOPT_HTTPHEADER, ["x-sdk-client" => "php/2.0.0"]);
        }

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        if (substr($url, 0, 5) == 'https') {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        }

        $rtn = curl_exec($ch);

        if ($rtn === false) {
            // 大多由设置等原因引起，一般无法保障后续逻辑正常执行，
            // 所以这里触发的是E_USER_ERROR，会终止脚本执行，无法被try...catch捕获，需要用户排查环境、网络等故障
            trigger_error("[CURL_" . curl_errno($ch) . "]: " . curl_error($ch), E_USER_ERROR);
        }
        curl_close($ch);
        return $rtn;//纯洁的返回
    }

}