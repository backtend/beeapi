<?php
declare (strict_types=1);

namespace app\backend\controller\oauth;

use app\backend\BaseController;

class IndexController extends BaseController
{
    public function index()
    {
        return '您好！这是一个[backend]示例应用';
    }
}
