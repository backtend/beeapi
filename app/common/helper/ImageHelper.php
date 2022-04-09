<?php
declare (strict_types=1);

namespace app\common\helper;


class ImageHelper
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
     * 缩小或放大图片
     */
    public static function resizeImage($im, $distx, $disty, $truecolor = true)
    {
        $x = imagesx($im);
        $y = imagesy($im);
        if ($x <= $distx and $y <= $disty)
            return $im;

        if ($x >= $y) {
            $newx = $distx;
            $newy = $newx * $y / $x;
        } else {
            $newy = $disty;
            $newx = $x / $y * $newy;
        }

        if ($truecolor) {
            //jpg等必须使用真彩色
            $imFinal = imagecreatetruecolor(intval($newx), intval($newy));
        } else {
            //png等可能透明，不能够使用truecolor，否则可能存在黑底
            $imFinal = imagecreate(intval($newx), intval($newy));
        }

        imagecopyresized($imFinal, $im, 0, 0, 0, 0, intval($newx), intval($newy), $x, $y);

        return $imFinal;
    }


    /**
     * 把图片数组转为html内容体
     */
    public static function imageToHtml($images)
    {
        //array:['aaaa.jpg','bbbb.jpg']
        //string:['aaaa.jpg','bbbb.jpg']
        //string:'aaaa.jpg'

        if ($images === null) {
            return '';
        }
        if (!is_array($images)) {
            $images = json_decode($images, true);
            $images = $images ?: [];
        }//halt($images);

        $content = '';
        //$formatter = '<img width="100%%" src="%s"/>';//必须两个百分比啊
        $formatter = '<img width="100%%" style="padding: 0;margin: 0;display:block;" src="%s"/>';
        foreach ($images as $item) {
            if (empty($item)) {
                continue;
            }
            $content .= sprintf($formatter, $item);
        }
        return $content;
    }

}