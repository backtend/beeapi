<?php
declare (strict_types=1);

namespace app;

use think\App;
use think\exception\ValidateException;
use think\Validate;

/**
 * 控制器基础类
 */
abstract class BaseController
{
    /**
     * Request实例
     * @var \think\Request
     */
    protected $request;

    /**
     * 应用实例
     * @var \think\App
     */
    protected $app;

    /**
     * 是否批量验证
     * @var bool
     */
    protected $batchValidate = false;

    /**
     * 控制器中间件
     * @var array
     */
    protected $middleware = [];

    /**
     * 构造方法
     * @access public
     * @param App $app 应用对象
     */
    public function __construct(App $app)
    {
        $this->app = $app;
        $this->request = $this->app->request;

        // 控制器初始化
        $this->initialize();
    }

    // 初始化
    protected function initialize()
    {
    }

    /**
     * 验证数据
     * @access protected
     * @param array $data 数据
     * @param string|array $validate 验证器名或者验证规则数组
     * @param array $message 提示信息
     * @param bool $batch 是否批量验证
     * @return array|string|true
     * @throws ValidateException
     */
    protected function validate(array $data, $validate, array $message = [], bool $batch = false)
    {
        if (is_array($validate)) {
            $v = new Validate();
            $v->rule($validate);
        } else {
            if (strpos($validate, '.')) {
                // 支持场景
                [$validate, $scene] = explode('.', $validate);
            }
            $class = false !== strpos($validate, '\\') ? $validate : $this->app->parseClass('validate', $validate);
            $v = new $class();
            if (!empty($scene)) {
                $v->scene($scene);
            }
        }

        $v->message($message);

        // 是否批量验证
        if ($batch || $this->batchValidate) {
            $v->batch(true);
        }

        return $v->failException(true)->check($data);
    }


    /**
     * CDO正规档RESTful
     * @param int $code
     * @param null $data
     * @param null $option
     * @return \think\response\Json
     */
    public function restful(int $code, $data = null, $option = null)
    {
        $defaultMsg = config(sprintf('custom.httpcode.%d', $code));
        if ($defaultMsg === null) {
            throw new \InvalidArgumentException('httpcode not support');
        }
        $code = $data === false ? 404 : $code;//halts(func_get_args());
        $msg = is_string($data) ? $data : (is_string($option) ? $option : ($defaultMsg ?: 'NotDefined'));
        if ((is_array($data) and isset($data[0])) or (is_array($data) and !sizeof($data)) or $data instanceof \think\Collection) {
            $data = ['list' => $data];
        } elseif (empty($data) or $data === true or is_string($data)) {
            $data = new \stdClass();//as object
        }
        //http code as same as json code value
        return json(['code' => $code, 'msg' => $msg, 'data' => $data, 'rid' => uniqid(sprintf('v202204-%.4f-', microtime(true)))])->code($code);
    }
}
