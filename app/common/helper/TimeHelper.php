<?php
declare (strict_types=1);

namespace app\common\helper;


class TimeHelper
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


    /**格式为可视化时间
     * @param null $time 时间戳或datetime
     * @param int $type
     * @param string $default
     * @return false|string
     */
    public static function see($time = null, $type = null, $default = '-')
    {
        if (empty($time)) {
            //return $time === null ? date('Y-m-d H:i:s') : $default;从未登录的情况是null
            return $default;
        }
        if (!is_numeric($time)) {
            $time = strtotime($time);
        }
        $timestamp = intval($time);
        if ($timestamp <= 0) {
            return $default;
        }

        //$result = '';
        switch ($type) {
            case 1 ://默认的，例如：2013-11-02 17:34:32
                $result = date('Y-m-d H:i:s', $timestamp);
                break;
            case 10 ://
                $result = date('Y年m月d日 H时i分s秒', $timestamp);
                break;
            case 11 ://
                $result = date('Y年m月d日 H时i分', $timestamp);
                break;
            case 12 ://
                $result = date('m月d日 H时i分', $timestamp);
                break;
            case 13 ://
                $result = date('Y年m月d日', $timestamp);
                break;
            case 14 ://
                $result = date('Y年m月', $timestamp);
                break;
            case 18 ://
                $result = date('Y年', $timestamp);
                break;
            case 20 ://
                $result = date('Y-m-d', $timestamp);
                break;
            case 21 ://
                $result = date('Y-m', $timestamp);
                break;
            default :
                //最经典的，较旧的时间：只时分，无秒，例如：2013-11-02 17:34
                $today = date('G') * 3600 + date('i') * 60 + date('s'); //今天过了多少秒
                $nowTime = time();
                $residue = $nowTime % $timestamp;
                if ($residue < 60) {
                    $result = ($residue == 0) ? '刚刚' : $residue . '秒前';
                } elseif ($residue < 3600) {
                    $result = floor($residue / 60) . '分钟前';
                } elseif ($residue <= $today) {
                    $result = '今天' . date('H:i', $timestamp);
                } elseif ($residue > $today && $residue < $today + 86400) {
                    $result = '昨天' . date('H:i', $timestamp);
                } elseif (date('Y', $timestamp) == date('Y')) {
                    $result = date('m-d H:i', $timestamp);
                } else {
                    $result = date('Y-m-d H:i', $timestamp);
                }
                break;
        }
        return $result;
    }


    /**
     * 根据类型获取时间戳范围数组（缺省今天）
     * @param string $type
     * @return array 数组[$startTime,$endTime]
     */
    public static function range(string $type = 'today')
    {
        switch ($type) {
            case 'yesterday'://昨日
                $startTime = strtotime(date('Y-m-d 00:00:00', strtotime('-1 day')));
                $endTime = strtotime(date('Y-m-d 23:59:59', strtotime('-1 day')));
                break;
            case 'this_week'://本周
                $startTime = mktime(0, 0, 0, date("m"), date("d") - date("w") + 1, date("Y"));;
                $endTime = time();
                break;
            case 'last_week'://上周
                $startTime = strtotime(date('Y-m-d 00:00:00', strtotime('last Sunday')));
                $endTime = strtotime(date('Y-m-d H:i:s', strtotime('last Sunday') + 7 * 24 * 3600 - 1));
                break;
            case 'this_month'://本月
                $startTime = strtotime(date('Y-m-01 00:00:00', time()));
                $endTime = time();
                break;
            case 'last_month'://上月
                //$startTime = strtotime(date('Y-m-01 00:00:00', strtotime('-1 month')));
                $startTime = strtotime(date('Y-m-d 00:00:00', strtotime('first day of -1 month')));
                //$endTime = strtotime(date('Y-m-31 23:59:00', strtotime('-1 month')));
                $endTime = strtotime(date('Y-m-01 00:00:00')) - 1;//当月0点减一秒
                break;
            case 'this_year'://本年度
                $startTime = strtotime(date("Y") . "-1" . "-1");
                $endTime = strtotime(date("Y") . "-12" . "-31");
                break;
            case 'last_year'://上年度
                $startTime = mktime(0, 0, 0, 1, 1, date('Y', strtotime('-1 year', time())));
                $endTime = mktime(0, 0, 0, 12, 31, date('Y', strtotime('-1 year', time())));
                break;
            default:
                $startTime = strtotime(date('Y-m-d 00:00:00'));
                $endTime = strtotime(date('Y-m-d 23:59:59'));
                break;
        }
        return [$startTime, $endTime];
    }


    /**
     * 按星期获取起止日期时间戳
     * 1:周一/2:周二/3:周三/4:周四/5:周五/6:周六/7:周日
     * 缺省:无
     * @param unknown $day
     * @return multitype:number
     */
    /*public static function weekRange($day)
    {
        $starttime = 0;
        $endtime = 1;
        switch ($day) {
            case 1:
                $starttime = mktime(0, 0, 0, date("m"), date("d") - date("w") + 1, date("Y"));
                $endtime = mktime(23, 59, 59, date("m"), date("d") - date("w") + 1, date("Y"));
                break;
            case 2:
                $starttime = mktime(0, 0, 0, date("m"), date("d") - date("w") + 2, date("Y"));
                $endtime = mktime(23, 59, 59, date("m"), date("d") - date("w") + 2, date("Y"));
                break;
            case 3:
                $starttime = mktime(0, 0, 0, date("m"), date("d") - date("w") + 3, date("Y"));
                $endtime = mktime(23, 59, 59, date("m"), date("d") - date("w") + 3, date("Y"));
                break;
            case 4:
                $starttime = mktime(0, 0, 0, date("m"), date("d") - date("w") + 4, date("Y"));
                $endtime = mktime(23, 59, 59, date("m"), date("d") - date("w") + 4, date("Y"));
                break;
            case 5:
                $starttime = mktime(0, 0, 0, date("m"), date("d") - date("w") + 5, date("Y"));
                $endtime = mktime(23, 59, 59, date("m"), date("d") - date("w") + 5, date("Y"));
                break;
            case 6:
                $starttime = mktime(0, 0, 0, date("m"), date("d") - date("w") + 6, date("Y"));
                $endtime = mktime(23, 59, 59, date("m"), date("d") - date("w") + 6, date("Y"));
                break;
            case 7:
                $starttime = mktime(0, 0, 0, date("m"), date("d") - date("w") + 7, date("Y"));
                $endtime = mktime(23, 59, 59, date("m"), date("d") - date("w") + 7, date("Y"));
                break;
        }
        return array($starttime, $endtime);
    }*/

    /**
     * 秒数可视化
     * @param $second
     * @param string $default
     * @return string
     */
    public static function timming($second, $default = '-')
    {
        if (!is_integer($second) or $second <= 0) {
            return $default;
        }
        if ($second <= 60) {
            return sprintf('%d秒', $second);
        } elseif ($second <= 3600) {
            return sprintf('%d分钟', round($second / 60));
        } elseif ($second <= 86400) {
            return sprintf('%d小时', round($second / 3600));
        }
        return sprintf('%d天', ceil($second / 86400));
    }


    /**
     * 获取日期时间（年月日时分秒）
     * @param null $timestamp
     * @return false|string
     */
    public static function datetime($timestamp = false)
    {
        if ($timestamp === null) {
            return '-';
        }
        $timestamp = $timestamp === false ? time() : $timestamp;
        if (!is_numeric($timestamp)) {
            $timestamp = strtotime($timestamp);
        }
        return date('Y-m-d H:i:s', intval($timestamp));
    }

    /**
     * 返回时间戳的区间（默认当天起止）
     * @param string $string 原始请求串
     * @param bool $returnTimestamp 返回时间戳
     * @return array
     */
    public static function between(string $string, $returnTimestamp = true): array
    {
        $timeRange = explode(',', $string);
        $startedAt = (isset($timeRange[0]) and is_numeric($timeRange[0])) ? $timeRange[0] : strtotime('today');
        $endedAt = $timeRange[1] ?? strtotime('tomorrow');
        if (!$returnTimestamp) {
            return [date('Y-m-d H:i:s', intval($startedAt)), date('Y-m-d H:i:s', intval($endedAt))];
        }
        return [intval($startedAt), intval($endedAt)];
    }


}