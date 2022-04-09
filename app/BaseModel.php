<?php
declare (strict_types=1);

namespace app;

use think\Model;
use think\model\concern\SoftDelete;

/**
 * 模型基础类
 */
abstract class BaseModel extends Model
{
    use SoftDelete;
}
