<?php
declare (strict_types=1);

namespace app\index\controller;

use app\index\BaseController;

class IndexController extends BaseController
{
    public function index()
    {
        try {
            $probing = \think\facade\Cache::store('file')->get('probing');
        } catch (\Throwable $e) {
            return $this->restful(502, 'Cache Failed');
        }

        if ($probing) {
            return $this->restful(200, $probing, 'Cache Info');
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
        //$info['MYSQL_DATETIME'] = current(current(\think\facade\Db::query('SELECT NOW();')));
        //$info['MYSQL_TIMEZONE'] = implode('/', array_column(\think\facade\Db::query('show variables LIKE "%time_zone%";'), 'Value'));

        //记入缓存
        \think\facade\Cache::store('file')->set('probing', $info, 3);

        return $this->restful(200, $info, sprintf('hey.%s', time()));
        //LAST.captcha-20210321

        //return '<style type="text/css">*{ padding: 0; margin: 0; } div{ padding: 4px 48px;} a{color:#2E5CD5;cursor: pointer;text-decoration: none} a:hover{text-decoration:underline; } body{ background: #fff; font-family: "Century Gothic","Microsoft yahei"; color: #333;font-size:18px;} h1{ font-size: 100px; font-weight: normal; margin-bottom: 12px; } p{ line-height: 1.6em; font-size: 42px }</style><div style="padding: 24px 48px;"> <h1>:) </h1><p> ThinkPHP V' . \think\facade\App::version() . '<br/><span style="font-size:30px;">14载初心不改 - 你值得信赖的PHP框架</span></p><span style="font-size:25px;">[ V6.0 版本由 <a href="https://www.yisu.com/" target="yisu">亿速云</a> 独家赞助发布 ]</span></div><script type="text/javascript" src="https://tajs.qq.com/stats?sId=64890268" charset="UTF-8"></script><script type="text/javascript" src="https://e.topthink.com/Public/static/client.js"></script><think id="ee9b1aa918103c4fc"></think>';
    }
}
