<?php
declare (strict_types=1);

namespace app;

use think\Validate;

class BaseValidate extends Validate
{

    /**
     * 获取允许字段名
     */
    public static function rules($class)
    {
        return array_keys((new $class())->rule);
    }
}