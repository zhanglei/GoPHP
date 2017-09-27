<?php
//默认视图配置
return [

    'driver'             => 'smarty', //视图驱动
    'error_template'     => VIEW_PATH . '/public/message', //错误消息模板
    'success_template'   => VIEW_PATH . '/public/message', //成功消息模板

    'smarty' => [
        'template_suffix' => 'html', //模板文件扩展名
        'left_delimiter'  => '{{', //左定界符
        'right_delimiter' => '}}', //右定界符
        'cache_enable'    => false, //是否缓存
        'cache_lifetime'  => 120, //缓存时间，单位秒
        'plugin_path'     => COMMON_PATH . '/smarty',

    ],


];

