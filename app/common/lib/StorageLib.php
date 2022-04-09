<?php
declare (strict_types=1);

namespace app\common\lib;

use think\Exception;
use think\exception\ValidateException;

/**
 * Class StorageLib
 * @package app\common\lib
 */
class StorageLib
{
    /**
     * 图裂缺省图
     */
    public static function crack()
    {
        return self::stated('crack.png');
    }

    /**
     * 拼接完整的url地址
     * @param $value
     * @param string $path
     * @return string
     */
    public static function stated($value, $path = 'default')
    {
        //$prefix = config('custom.default.cdn_url');//halt($prefix);
        $prefix = '';//halt($prefix);
        return sprintf('%s/stated/%s/%s', $prefix, $path, $value);
    }


    /**
     * 图片生产工厂
     */
    public static function picturer(string $baseApi, $filename, array $params = [])
    {
        $param = count($params) == 0 ? '' : '?' . http_build_query($params);
        return sprintf('%s/index/picturer/img/%s%s', $baseApi, $filename, $param);
    }


    /**
     * 获取字体
     */
    public static function ttf()
    {
        return root_path() . 'app/common/fonts/puhuiti_heavy.ttf';
    }


    /**
     * oss图片路径转换cdn（单图）
     */
    /*public static function transfer(string $value)
    {
        $ossPrefix = 'http://example.oss-cn-hangzhou.aliyuncs.com';
        $cdnPrefix = config('custom.default.domain_cdn');
        return str_replace($ossPrefix, $cdnPrefix, $value);
    }*/

    /**
     * oss图片路径转换cdn（多图）
     */
    /*public static function transfers($data): array
    {
        if (empty($data)) {
            return [];
        }
        if (!is_array($data)) {
            $data = json_decode($data, true);
        }
        $result = [];
        foreach ($data as $item) {
            $result[] = self::transfer($item);
        }
        return $result;
    }*/


    /**
     * 图片库相对路径转换（单图）
     */
    /*public static function image($value)
    {
        if (!preg_match("/^(http:|https:)?\/\/[-A-Za-z0-9+&@#\.\/]+/i", $value)) {
            //$prefix = config('custom.default.domain_cdn');
            return $prefix . '' . $value;
        }
        return $value;
    }*/

    /**
     * 图片库相对路径转换（多图）
     */
    /*public static function images($value): array
    {
        if (empty($value)) {
            return [];
        }
        if (!is_array($value)) {
            $value = json_decode($value, true);
        }
        $data = [];
        foreach ($value as $item) {
            $data[] = self::image($item);
        }
        return $data;
    }*/


    /**
     * 获取上传器的上传参数
     */
    public static function uploader($category, $channel): array
    {
        //根据通道channel获取站点命名所属
        $baseApi = SiteLogic::baseApi(current(explode('.', $channel)));//haltd($baseApi);

        $timestamp = CommonLogic::timestamp();
        $salt = config('custom.default.project_salt');
        $state = base64_encode(json_encode(['user_id' => Authy::$UID]));//回笼参数
        //签名方法：md5(当前时间戳timestamp+文件通道channel+回笼参数state+固定盐值salt)
        $sign = md5($timestamp . $category . $channel . $state . $salt);

        $data = [
            'url' => sprintf('%s%s', $baseApi, config('custom.uploader.receive_url')),
            'params' => [
                'timestamp' => $timestamp,
                'category' => $category,
                'channel' => $channel,
                'state' => $state,//state=json
                'sign' => $sign,
            ]
        ];
        return $data;
    }

    /**
     * 解析上传参数
     */
    public static function uploaded(array $input): \stdClass
    {
        $sign = $input['sign'] ?? '';//签名
        $timestamp = $input['timestamp'] ?? 0;//时间戳
        $category = $input['category'] ?? '';//分类类别
        $channel = $input['channel'] ?? '';//通道
        $state = $input['state'] ?? '';//回笼参数

        $salt = config('custom.default.project_salt');
        //签名方法：md5(当前时间戳timestamp+文件通道channel+回笼参数state+固定盐值salt)
        $signNew = md5($timestamp . $category . $channel . $state . $salt);
        if ($sign !== $signNew) {
            throw new ValidateException('签名参数出错');
        }
        if ($timestamp < time() - 43200) {
            throw new ValidateException('时间戳参数过期');//半天已经很宽容了
        }
        $result = new \stdClass();
        $result->category = $category;
        $result->channel = $channel;
        $result->state = json_decode(base64_decode($state));
        /*$data = [
            'category' => $category,
            'channel' => $channel,
            'state' => json_decode($state, true),
        ];*/

        return $result;
    }

    /**
     * oss上传url
     */
    public static function putOssUrl(): string
    {
        //
        return AliyunParty::putOssUrl();
    }


    /**
     * oss查看url
     */
    public static function getOssUrl(): string
    {
        //
        return AliyunParty::getOssUrl();
    }


    /**
     * 输出图像响应
     * @param $im
     * @param float $qrate
     * @param string $type
     * @return \think\Response
     * @throws Exception
     */
    public static function getImageResponse($im, float $qrate = 0.98, string $type = 'jpg')
    {
        if ($qrate > 1 or $qrate < 0.02) {
            throw new ValidateException('输出质量要求百分率，不得大于一');
        }

        switch ($type) {
            case 'jpg':
                $mime = 'image/jpeg';
                $quality = round($qrate * 100);
                break;
            case 'png':
                $mime = 'image/png';
                $quality = round($qrate * 10 - 1);
                break;
            default:
                throw new Exception('获取图片响应的图片类型仅支持png和jpg');
                break;
        }

        /*
        //获取图形流指针
        ob_start();//直接输出到缓冲区
        imagejpeg($im, null, intval($quality));//quality输出质量(jpg=0-100,png=0-9)
        $content = ob_get_clean();//获取缓冲区输出并清除
        imagedestroy($im);//释放内存
        //return $content;*/


        ob_start();//直接输出到缓冲区
        imagejpeg($im, null, intval($quality));//quality输出质量(jpg=0-100,png=0-9)
        $content = ob_get_contents();//获取缓冲区输出
        ob_end_clean();//清除
        imagedestroy($im);//释放内存

        return response($content, 200, ['Content-Length' => strlen($content)])->contentType($mime);
    }


    /**
     * 删除文件
     * @param $url
     * @return bool
     */
    public static function remove($url): bool
    {
        //http://cdns.example.com/uploads/offline-env-dev/image/mp/chain/payoff/20210425/48a788ba75b6558af3f7ee3bd1eac008.png
        $urls = parse_url($url);
        $urlPath = $urls['path'] ?? '';
        if (empty($urlPath)) {
            return false;
        }
        $realPath = sprintf('%s%s', root_path(), $urlPath);
        if (!file_exists($realPath)) {
            return false;
        }

        //删除文件$realPath
        unlink($realPath);

        return true;
    }

}
