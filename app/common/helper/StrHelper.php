<?php
declare (strict_types=1);

namespace app\common\helper;


class StrHelper
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
     * 正则判断手机号码
     * @param unknown $str
     * @return boolean
     */
    public static function isMobile($str)
    {
        // 手机号码 '/^(\+86|86)?1[35678][0-9]{9}/';
        // IP '/^(25[0-5]|2[0-4][0-9]|1[0-9][0-9]|[0-9]{1,2})(\.(25[0-5]|2[0-4][0-9]|1[0-9][0-9]|[0-9]{1,2})){3}$/';
        // 400电话 '/^(400)[016789][0-9]{6}/';
        // 中国座机 '/^(\+86|86|0)((\d{2}-\d{8})|(\d{3}-\d{7})|(\d{10}))/';
        // qq号 '/^\d{5,16}/';
        // HTML备注 '/<!--.*-->/U';
        return (preg_match('/^(\+86|86)?1[3456789][0-9]{9}$/', strval($str))) ? true : false;
    }


    /**
     * 是否电话号码
     * @param unknown $str
     * @return boolean
     */
    public static function isTelephone($str)
    {
        return (preg_match('/^(\+86|86|0)((\d{2}-\d{8})|(\d{3}-\d{7})|(\d{10}))$/', $str)) ? true : false;
    }


    /**
     * 是否ip判断
     * @param unknown $str
     * @return boolean
     */
    public static function isIp($str)
    {
        $regular = '/^(25[0-5]|2[0-4][0-9]|1[0-9][0-9]|[0-9]{1,2})(\.(25[0-5]|2[0-4][0-9]|1[0-9][0-9]|[0-9]{1,2})){3}$/';
        return (preg_match($regular, $str)) ? true : false;
    }


    /**
     * 是否email判断
     * @param unknown $str
     * @return boolean
     */
    public static function isEmail($str)
    {
        return (preg_match("/^([0-9A-Za-z\\-_\\.]+)@([0-9a-z]+\\.[a-z]{2,3}(\\.[a-z]{2})?)$/i", $str)) ? true : false;
    }


    /**
     * 是否登录帐号
     * @return boolean
     */
    public static function isAccount($str)
    {
        return (preg_match("/^[a-zA-Z][0-9a-zA-Z_\s]+$/", $str)) ? true : false;
    }

    /**
     * 是否超链接url
     * @return boolean
     */
    public static function isUrl($str)
    {
        return (preg_match("/^(https?|ftp|file):\/\/[-A-Za-z0-9+&@#\/\%?=~_|!:,.;]+[-A-Za-z0-9+&@#\/\%=~_|]/i", $str)) ? true : false;
    }

    /**
     * 是否纯英文
     */
    public static function isEnglish($str)
    {
        //return (preg_match("/^[^/x80-/xff]+$/", $str)) ? true : false;
        return (preg_match("@^\w+$@", $str)) ? true : false;
    }

    /**
     * 是否纯中文
     */
    public static function isChinese($str)
    {
        //备用preg_match('/^[\x{4e00}-\x{9fa5}]+$/u',$str)
        return (preg_match("/^[\x7f-\xff]+$/", $str)) ? true : false;
    }

    /**
     * 是否20xx开头的流水序列号
     */
    public static function isSeriesNumber($str)
    {
        //$pattern = sprintf("/^20\d{18,32}$/");
        return (preg_match("/^20\d{17,30}$/", $str)) ? true : false;
    }


    /**
     * 对象转数组,使用get_object_vars返回对象属性组成的数组
     */
    public static function objectToArray($data)
    {
        if (is_object($data)) {
            $res = array();
            foreach ($data as $k => $v) {
                $res[$k] = self::objectToArray($v);
            }
            return count($res) > 0 ? $res : $data;
        }
        return $data;
        /*
        $arr = is_object($obj) ? get_object_vars($obj) : $obj;
        if(is_array($arr)){
            return array_map(__FUNCTION__, $arr);
        }
        return $arr;
         */
    }

    /**
     * 数组转对象
     * @param unknown $arr
     * @return StdClass|unknown
     */
    public static function arrayToObject($data)
    {
        if (is_array($data)) {
            $res = new stdClass();
            foreach ($data as $k => $v) {
                $res->$k = self::arrayToObject($v);
            }
            return $res;
        }
        return $data;

        /*
        if(is_array($arr)){
            return (object)array_map(__FUNCTION__, $arr);
        }
        return $arr;
         */
    }

    /**
     * 递归转换json为数组
     */
    public static function jsonToArray($data)
    {
        if (is_string($data) and is_array(json_decode($data, true))) {
            $data = json_decode($data, true);
            foreach ($data as $k => $v) {
                $data[$k] = self::jsonToArray($v);
            }
        }
        return $data;
    }


    /**
     * 字符串二进制base64编码转数组或对象,失败就false
     * @param base64 $str base64编码的串
     * @param boolean $fa 需要对象此项为空需要数组为TRUE
     * @return mixed|boolean
     */
    public static function base64Decode($str, $fa = false)
    {
        if ($str == base64_encode(base64_decode($str))) {
            $str = base64_decode($str);
            $res = $fa ? json_decode($str, TRUE) : json_decode($str);
            if (json_last_error() == JSON_ERROR_NONE) {
                return $res;
            }
        }
        return false;
    }

    /**
     * 字符串或数组变成base64编码,失败就false
     * @param base64 $data 数组或字符串
     * @return mixed|boolean
     */
    public static function base64Encode($data)
    {
        try {
            if (is_array($data)) {
                $data = json_encode($data);
            }
            $str = base64_encode($data);
            if (self::base64Decode($str) !== false) {
                return $str;
            }
        } catch (\Exception $e) {
            //
        }
        return false;
    }


    /**
     * 可逆加密解密算法
     * @param unknown $string 加密的串
     * @param string $operation ENCODE|DECODE
     * @param string $key 混肴值
     * @param number $expiry 失效秒数
     * @return string
     */
    public static function authcode($string, $operation = 'DECODE', $key = '', $expiry = 0)
    {
        $ckey_length = 4;
        $keyp = defined('AUTHKEYP') ?? Cookie::$salt;
        $key = md5($key ? $key : $keyp);
        $keya = md5(substr($key, 0, 16));
        $keyb = md5(substr($key, 16, 16));
        $keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length) : substr(md5(microtime()), -$ckey_length)) : '';
        $cryptkey = $keya . md5($keya . $keyc);
        $key_length = strlen($cryptkey);
        $string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0) . substr(md5($string . $keyb), 0, 16) . $string;
        $string_length = strlen($string);
        $result = '';
        $box = range(0, 255);
        $rndkey = array();
        for ($i = 0; $i <= 255; $i++) {
            $rndkey[$i] = ord($cryptkey[$i % $key_length]);
        }
        for ($j = $i = 0; $i < 256; $i++) {
            $j = ($j + $box[$i] + $rndkey[$i]) % 256;
            $tmp = $box[$i];
            $box[$i] = $box[$j];
            $box[$j] = $tmp;
        }
        for ($a = $j = $i = 0; $i < $string_length; $i++) {
            $a = ($a + 1) % 256;
            $j = ($j + $box[$a]) % 256;
            $tmp = $box[$a];
            $box[$a] = $box[$j];
            $box[$j] = $tmp;
            $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
        }
        if ($operation == 'DECODE') {
            if ((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26) . $keyb), 0, 16)) {
                return substr($result, 26);
            } else {
                return '';
            }
        } else {
            return $keyc . str_replace('=', '', base64_encode($result));
        }
        return '';
    }

    /**
     * bsykc-获取在一定空间和时间内的唯一序列号
     * Tools::seriesNumber(16,'input');
     * @param number $length 随意长度
     * @param string $type 类型
     * @param string $prefix 前缀
     * @return string
     * @since 20170608
     */
    public static function seriesNumber($length = 32, $type = '', $prefix = '')
    {
        $sn = '';/* 选择一个随机的方案 */
        switch ($type) {
            case 'input'://被输入的全大写，不允许有I1O0
                //62个字符0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ
                $str = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';//32种子
                $prelen = strlen($prefix);
                if ($prelen > 0) {
                    $sn = $prefix;
                    $length -= $prelen;
                }
                for ($i = 0; $i < $length; $i++) {
                    $sn .= substr($str, rand(0, 31), 1);
                }
                break;

            case 'share':
                $sn = rand(100, 999);
                break;

            case 'token':
                $sn = md5(uniqid());
                break;

            case 'hadoop':
                //时空分布式原子发生器字符串
                $sn = uniqid(date('YmdHis')) . random_int(10000, 99999);
                break;

            default://32位以时间为主轴的(充值或订单)序列号
                list($usec, $sec) = explode(" ", microtime());
                //$sn .= date($length < 32 ? 'ymdHis' : 'YmdHis');
                $sn .= date('YmdHis');
                $sn .= ceil(($usec + rand(1, 9)) * 1000000);
                $sn .= mt_rand(1000000, 9999999);
                $sn .= rand(1000, 9999);
                for ($i = strlen($sn); $i < $length; $i++) {
                    $sn .= substr(strval(rand(1000, 9999)), 0, 1);
                }
                $sn = substr($sn, 0, $length);//秒安全位为14位，百毫秒安全位一般为16
                break;
        }
        return $sn;
    }

    /**
     * 获取大数值的缩写
     * @param $num
     * @return string
     */
    public static function seeNum($num)
    {
        if (!is_numeric($num)) {
            return $num;
        }

        if ($num > 1000 and $num < 100000) {
            //大于0.1万小于100万
            return round($num / 10000, 2) . '万';
        } elseif ($num >= 100000 and $num < 10000000) {
            //大于10万小于100万
            return round($num / 10000, 1) . '万';
        } elseif ($num >= 10000000 and $num < 100000000) {
            //大于10万小于100万
            return round($num / 10000) . '万';
        } elseif ($num >= 100000000) {
            //大于1亿
            return round($num / 100000000, 2) . '亿';
        }
        return $num;
    }


    /**
     * 传入文件字节数返回可视化大小
     * @param $size
     * @return string
     */
    public static function filesize($size)
    {
        if (!is_numeric($size)) {
            return '-';
        }

        if ($size < 10) {
            return $size . 'B';
        } elseif ($size < 10000) {
            return round($size / 1000, 2) . 'KB';
        } elseif ($size < 1000000000) {
            return round($size / 1000000, 2) . 'MB';
        } else {
            return round($size / 1000000000, 2) . 'GB';
        }
    }

    /**
     * 清除html格式为有换行的纯文本
     * @param $str
     * @return string|string[]|null
     */
    public static function clearHtml($str)
    {
        $str = trim(html_entity_decode(strip_tags($str, '<div><p><br>')));
        $str = preg_replace('/\<\/?(p|br|div)[^\>]*\>/i', "\n", $str);
        $str = trim(preg_replace('/[\n]{2,}/', "\n", $str), "\n");
        return $str;
    }


    /**
     * 判断字符串是否可转化为json
     * @param $str
     * @return bool
     */
    public static function isJson($str)
    {
        $data = json_decode($str);
        if (($data and is_object($data)) or (is_array($data) and !empty($data))) {
            //if ($data and is_object($data)) {
            return true;
        }
        return false;
    }


    /**
     * 获取绝对数字，哪怕传入的是字符串
     * @param $pk 数字或字符串
     * @param $salt 附加值
     * @return mixed 返回一个关联integer
     */
    public static function fixedNumber($pk, string $salt = ''): int
    {
        if (is_int($pk)) {
            return $pk;//若是整型直接返回
        }
        $m1 = md5(strval($pk));
        $m2 = md5($m1 . strval($pk) . $salt);
        preg_match_all('/\d+/', $m1 . $m2, $match);//dump(array_sum(current($match)));
        return (int)array_sum(current($match));
    }


    /**
     * 传入版本字符串返回百进制版本整数
     * @param $name
     * @return float|int
     */
    public static function appInteger($name)
    {
        $va = explode('.', $name);//dump($va);
        if (count($va) != 3) {
            return 10000;
        }
        $newer = intval($va[0]) * 10000;
        $newer += intval($va[1]) * 100;
        $newer += intval($va[2]);
        return $newer;
    }


    /**
     * 产生一个token令牌
     * @param string $salt
     * @return string
     */
    public static function token($salt = '')
    {
        return md5($salt . uniqid() . time());
    }


    /**
     * 从md5数据里面提取数字，来作为验证码，默认生成4位数字
     * @param int $length
     * @param string $keyword
     * @return string
     */
    public static function timecode($length = 4, $keyword = '')
    {
        $str = md5($keyword . time());
        $leng = 32;
        $code = '';
        $count = 0;

        for ($i = 3; $i < $leng; $i++) {
            $c = substr($str, $i, 1);
            if ($c >= '0' && $c <= '9') {
                $code = $code . $c;
                $count++;
                if ($count >= $length) {
                    break;
                }
            }
        }
        return $code;
    }


    /**
     * 传入类对象返回点号隔开的命名空间
     * 标准用法：$redisKey = StrHelper::ns(__METHOD__, 'product', $id);
     * @param $class
     * @param string $suffix1 可选后缀
     * @param string $suffix2 可选二次唯一
     * @return mixed|string
     */
    public static function ns()
    {
        $args = func_get_args();
        $ns = '';
        $seeds = ['\\', ':', '/'];
        $seeds = array_merge($seeds, ['.....', '....', '...', '..']);
        foreach ($args as $item) {
            $tmp = strval($item);
            foreach ($seeds as $seed) {
                $tmp = str_replace($seed, '.', $tmp);
            }
            $ns .= $tmp . '.';
        }
        return $ns ?: 'app.common.helper.StrHelper.ns.null.';
    }


    /**
     * 获取不重复字符串
     */
    public static function uuid()
    {
        return strrev(strtoupper(uniqid()));//？且没有0OIl之类？
    }

    /**
     * 清除所有中英文符号（pure为真则连空格都不要）
     */
    public static function clearSymbol($str, $pure = false)
    {
        $chars = "。、！？：；﹑•＂…‘’“”〝〞∕¦‖—　〈〉﹞﹝「」‹›〖〗】【»«』『〕〔》《﹐¸﹕︰﹔！¡？¿﹖﹌﹏﹋";
        $chars .= "＇´ˊˋ―﹫︳︴¯＿￣﹢﹦﹤‐­˜﹟﹩﹠﹪﹡﹨﹍﹉﹎﹊ˇ︵︶︷︸︹︿﹀︺︽︾ˉ﹁﹂﹃﹄︻︼（）";//中文标点
        $pattern = array("/[[:punct:]]/i", '/[' . $chars . ']/u', '/[ ]{2,}/');//英文标点符号，中文标点符号
        if ($pure) {
            return preg_replace($pattern, '', $str);
        }
        return preg_replace($pattern, ' ', $str);
    }


    /**
     * @param $data 需要加密的明文数据
     * @param $method 模式：DES-ECB/DES-CBC/DES-CTR/DES-OFB/DES-CFB/des-ede3-cbc
     * @param $key 加密密钥passwd
     * @param int $options 数据格式选项[0,1,2,3] OPENSSL_RAW_DATA|OPENSSL_ZERO_PADDING|OPENSSL_NO_PADDING
     * @param string $iv 加密初始化向量
     * @param null $tag
     * @param string $aad
     * @param int $tag_length
     * @return string
     */
    public static function desEncode($data, $method, $key, $options = 0, $iv = "")
    {
        //https://blog.csdn.net/zhemejinnameyuanxc/article/details/83383434
        return openssl_encrypt($data, $method, $key, $options, $iv);
    }

    /**
     * 需要解密的密文
     */
    public static function desDecode($data, $method, $password, $options = 1, $iv = "")
    {
        return openssl_decrypt($data, $method, $password, $options, $iv);
    }


    /**
     * 隐藏某些字符串
     * @param string $contact
     * @return mixed
     */
    public static function hidden(string $contact)
    {
        if (self::isMobile($contact)) {
            return substr_replace($contact, '****', 3, 4);//隐藏手机号
        }

        $strlen = mb_strlen($contact);
        if ($strlen == 2) {
            return mb_substr($contact, 0, 1) . '*';
        } elseif ($strlen < 2) {
            return $contact . '*';
        }
        return sprintf('%s*%s', mb_substr($contact, 0, 1), mb_substr($contact, -1, 1));
    }


    /**
     * 系统向下取整拓展函数
     * @param $float 需要向下的值
     * @param int $precision 精度
     * @return float|int
     */
    public static function floor($float, int $precision = 0)
    {
        /*
         * 高精度运算
        bcadd — 将两个高精度数字相加
        bccomp — 比较两个高精度数字，返回-1, 0, 1
        bcdiv — 将两个高精度数字相除
        bcmod — 求高精度数字余数
        bcmul — 将两个高精度数字相乘
        bcpow — 求高精度数字乘方
        bcpowmod — 求高精度数字乘方求模，数论里非常常用
        bcscale — 配置默认小数点位数，相当于就是Linux bc中的”scale=”
        bcsqrt — 求高精度数字平方根
        bcsub — 将两个高精度数字相减
         */
        if ($precision < 0) {
            return $float;
        } elseif ($precision === 0) {
            return floor($float);
        }
        $piece = pow(10, $precision);
        $res = bcmul(strval($float), strval($piece));
        return floor($res) / $piece;
    }


    /**
     * 解析短键值对的串（冒号键值逗号分割，如rid:900,otv:123返回数组）
     */
    public static function parseShorting(string $str): array
    {
        $res = [];
        $all = explode(',', $str);
        foreach ($all as $item) {
            $arr = explode(':', $item);
            if (count($arr) != 2) {
                continue;
            }
            if (empty($arr[0])) {
                continue;
            }
            $res[$arr[0]] = $arr[1];
        }
        return $res;
    }

}