<?php

namespace app;

use think\db\exception\DataNotFoundException;
use think\db\exception\ModelNotFoundException;
use think\exception\Handle;
use think\exception\HttpException;
use think\exception\HttpResponseException;
use think\exception\ValidateException;
use think\Response;
use Throwable;

/**
 * 应用异常处理类
 */
class ExceptionHandle extends Handle
{
    /**
     * 不需要记录信息（日志）的异常类列表
     * @var array
     */
    protected $ignoreReport = [
        HttpException::class,
        HttpResponseException::class,
        ModelNotFoundException::class,
        DataNotFoundException::class,
        ValidateException::class,
    ];

    /**
     * 记录异常信息（包括日志或者其它方式记录）
     *
     * @access public
     * @param Throwable $exception
     * @return void
     */
    public function report(Throwable $exception): void
    {
        // 使用内置的方式记录异常日志
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @access public
     * @param \think\Request $request
     * @param Throwable $e
     * @return Response
     */
    public function render($request, Throwable $e): Response
    {
        // 添加自定义异常处理机制

        $rid = uniqid(sprintf('v000000-%s-', microtime(true)));

        /*halts($request->isJson());//获取不到Content-Type:  application/json
        if ($request->isJson()){
            $data = Environ::isOnline()?(new \stdClass()):$e->getTrace();//为空硬是搞成空对象，确保data是对象
            return json(['code' => 504, 'msg' => $e->getMessage(), 'data' => $data, 'rid' =>$rid])->code(504);
        }*/


        //数据库链接异常：SQLSTATE[HY000] [2002] Connection refused
        if ($e instanceof \PDOException or $e instanceof \think\db\exception\PDOException) {
            $msg = substr($e->getMessage(), 0, 200);
            \think\facade\Log::error($e->getMessage() . PHP_EOL . $e->getTraceAsString());
            return json(['code' => 504, 'msg' => $msg, 'data' => (new \stdClass()), 'rid' =>$rid])->code(504);
        }

        //错误捕获，如Access to undeclared static property
        if ($e instanceof \Error) {
            $msg = substr($e->getMessage(), 0, 200);
            \think\facade\Log::error($e->getMessage() . PHP_EOL . $e->getTraceAsString());
            return json(['code' => 504, 'msg' => $msg, 'data' => (new \stdClass()), 'rid' =>$rid])->code(504);
        }

        // 参数验证错误
        if ($e instanceof ValidateException) {
            return json(['code' => 401, 'msg' => $e->getError(), 'data' => (new \stdClass()), 'rid' =>$rid])->code(401);
        }

        // 网络请求异常（基类之前finish的exit改用异常机制）
        if ($e instanceof HttpException) {
            return json(['code' => $e->getStatusCode(), 'msg' => $e->getMessage(), 'data' => (new \stdClass()), 'rid' =>$rid])->code($e->getStatusCode());
        }

        //数据不存在异常data not found
        if ($e instanceof DataNotFoundException) {
            return json(['code' => 404, 'msg' => $e->getMessage(), 'data' => (new \stdClass()), 'rid' =>$rid])->code(404);
        }

        //模型不存在异常model not found
        if ($e instanceof ModelNotFoundException) {
            //return parent::render($request, $e);
            $msg = \backtend\phpenv\Environ::isOffline() ? sprintf('ModelNotFound[%s]', $e->getMessage()) : 'ModelNotFoundError';
            return json(['code' => 404, 'msg' => $msg, 'data' => (new \stdClass()), 'rid' =>$rid])->code(404);
        }

        //未定义数组索引: dd 等error服务暂不可用 and Clienty::vuejs()
        if ($e instanceof \think\exception\ErrorException) {
            $msg = $e->getMessage();// Environ::isOffline() ? $e->getMessage() : '系统繁忙稍后再试';
            return json(['code' => 503, 'msg' => $msg, 'data' => (new \stdClass()), 'rid' =>$rid]);
        }

        //函数参数未提供: 如控制器参数InvalidArgumentException
        if ($e instanceof \InvalidArgumentException) {
            return json(['code' => 503, 'msg' => $e->getMessage(), 'data' => (new \stdClass()), 'rid' =>$rid])->code(503);
        }

        // 其他错误交给系统处理
        return parent::render($request, $e);
    }
}
