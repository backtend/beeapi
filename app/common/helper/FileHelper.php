<?php
declare (strict_types=1);

namespace app\common\helper;


class FileHelper
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
     * 递归创建文件夹(慎用)
     * @param string $directory
     * @param number $chmod
     * @return boolean
     */
    public static function mkdir($directory, $chmod = 0644)
    {
        return (is_dir($directory) AND is_writable($directory))
            OR (
                self::mkdir(dirname($directory), $chmod)
                AND (
                    mkdir($directory, 0777) OR chmod($directory, $chmod)
                )
            );
    }

}