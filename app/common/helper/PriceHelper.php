<?php
declare (strict_types=1);

namespace app\common\helper;


class PriceHelper
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
     * 向上混淆价格（非整5整0的，靠近5的算五，超过5的进位）
     * @param $price
     * @return float
     */
    public static function confuse($price)
    {
        $price = ceil($price);//halt($item,($item%10));
        $leftValue = ($price % 10);
        if ($leftValue !== 0) {
            $price = $price > 5 ? (ceil($price / 10) * 10 + 0) : (floor($price / 10) * 10 + 5);
        }
        return floatval($price);
    }


}