<?php
declare (strict_types=1);

namespace app\frontend\controller\index;

use app\frontend\BaseController;

class IndexController extends BaseController
{
    public function index()
    {
        return '您好！这是一个[frontend]示例应用';
    }
}
