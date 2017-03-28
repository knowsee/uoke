<?php

/**
 * @author Knowsee
 */
$_config['charset'] = 'utf8';
$_config['timezone'] = 'Asia/Shanghai';
$_config['cookies'] = [
    'domain' => 'hostlocal.com',
    'lifetime' => 3600,
    'prefix' => 'host_',
    'path' => '/',
    'safe' => '',
];
$_config['defaultAction'] = array(
    'siteIndex' => [
        'module' => 'Index',
        'action' => 'Index',
    ],
    'actionIndex' => 'Index',
);
$_config['urlRule'] = array(
    'type' => 3,
    'path' => [
        'Api_hl' => '/do/',
    ],
    'staticUrl' => [],
    'handleClass' => [
        1 => '\Factory\Uri\DefaultRule',
        2 => '\Factory\Uri\PathInfoRule',
        3 => '\Factory\Uri\RewriteRule',
    ]
);
$_config['siteUrl'] = array(
    'default' => 'http://host.ckc.im/',
    'Ip' => 'http://hostlocal.com/',
    'Admin' => 'http://adminlocal.com/'
);
$_config['data']['dir'] = 'Data/Upload/';
$_config['templateDir'] = 'Tmp/'; //{APP_DIR}/core/{YOUR TEMPLATE DIR NAME}}
$_config['timeFormat']['AiString'] = '@time@msg';
$_config['timeFormat']['default'] = 'Y-m-d';
$_config['timeFormat']['AiDiff'] = [
    10, 30, 60, 1440, 10080,
];
$_config['timeFormat']['lang'] = [
    10 => '10分钟',
    30 => '30分钟',
    60 => '60分钟',
    1440 => '一天',
    10080 => '一周',
    'after' => '之前',
    'before' => '之后',
];
