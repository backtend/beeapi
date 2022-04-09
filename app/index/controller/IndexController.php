<?php
declare (strict_types=1);

namespace app\index\controller;

use app\index\BaseController;

class IndexController extends BaseController
{
    public function index()
    {
        try {
            if ($probing = \think\facade\Cache::store('file')->get(__METHOD__)) {
                return $this->restful(200, $probing, 'Cache Info');
            }
        } catch (\Throwable $e) {
            return $this->restful(502, 'Cache Failed');
        }

        //phpinfo();die;
        $info = [];
        $info['APP_DEBUG'] = env('APP_DEBUG');
        $info['APP_ENVIRON'] = \backtend\phpenv\Environ::tag();

        #mysql check
        $mysql = 'ok';
        try {
            \think\facade\Db::query('SELECT md5("xxx")');
        } catch (\Throwable $e) {
            $mysql = $e->getMessage();
        }

        #redis check
        $redis = 'ok';
        try {
            \backtend\phplib\RedisLib::instance()->set('name', 'value', 3600);
        } catch (\RedisException $e) {
            $redis = $e->getCode();
        }//dump($redis);

        //runtime folder excpetion can not be cache
        #cache check
        $cache = 'ok';
        try {
            \think\facade\Cache::store('file')->set('name', 'value', 3600);
        } catch (\Throwable $e) {
            $cache = $e->getMessage();//HTTP ERROR 500
        }

        #log check
        $log = 'ok';
        try {
            \think\facade\Log::write('test log');
        } catch (\Throwable $e) {
            $log = $e->getMessage();
        }

        #captcha check
        $captcha = 'ok';
        try {
            //$im = imagecreatefromjpeg(root_path() . 'storage/stated/default/crack.jpg');
            //imagedestroy($im);//destroy
            $im = imagecreatefrompng(root_path() . 'storage/stated/default/crack.png');
            imagedestroy($im);//destroy
        } catch (\Throwable $e) {
            $captcha = $e->getMessage();
        }

        $info['CONNECT_MYSQL'] = $mysql;
        $info['CONNECT_REDIS'] = $redis;
        $info['CACHE_STATUS'] = $cache;
        $info['LOG_STATUS'] = $log;
        $info['CAPTCHA_STATUS'] = $captcha;

        //halt($_SERVER);
        $info['HTTP_X_REAL_IP'] = $_SERVER['HTTP_X_REAL_IP'] ?? '-';
        $info['REMOTE_ADDR'] = $_SERVER['REMOTE_ADDR'] ?? '-';
        $info['PHP_DATETIME'] = date('Y-m-d H:i:s');
        $info['PHP_TIMEZONE'] = date_default_timezone_get();
        $info['MYSQL_DATETIME'] = current(current(\think\facade\Db::query('SELECT NOW();')));
        $info['MYSQL_TIMEZONE'] = implode('/', array_column(\think\facade\Db::query('show variables LIKE "%time_zone%";'), 'Value'));

        //记入缓存
        \think\facade\Cache::store('file')->set(__METHOD__, $info, 1);

        return $this->restful(200, $info, sprintf('hey.%s', time()));
    }
    //LAST.probing-20220409
}
