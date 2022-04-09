<?php
// +----------------------------------------------------------------------
// | 自定义设置
// +----------------------------------------------------------------------

return [
    // 默认
    'default' => [
        'cdn_url' => env('custom.default_cdn_url', ''),
    ],
    //查询结果
    'results' => [
        'default_limit' => 50,
        'max_limit' => 250,
    ],
    //httpcode状态码翻译
    'httpcode' => [
        '200' => 'Success',
        '400' => 'RequestException',
        '401' => 'ValidateFailure',
        '403' => 'Rejected',
        '404' => 'NotFound',
        '405' => 'MethodNotSuit',
        '410' => 'ResourceOffline',
        '429' => 'TooManyRequest',
        '500' => 'InternalError',
        '502' => 'AgencyErrorToRetry',
        '503' => 'AgencyOverdueToRetry',
        '504' => 'AgencyTimeoutToRetry',
    ],
];
