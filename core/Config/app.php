<?php
/**
 * @author Knowsee
 */
$_config['charset'] = 'utf8';
$_config['timezone'] = 'Asia/Shanghai';
$_config['cookies'] = array(
    'domain' => '',
    'lifetime' => 3600,
    'prefix' => '',
    'path' => '',
    'safe' => '',
);
$_config['gpc'] = array(
    'clean' => '',
    'systemString' => '',
);
$_config['defaultAction'] = array(
    'siteIndex' => array(
        'module' => 'Index',
        'action' => 'Index',
    ),
    'actionIndex' => 'Index',
);
$_config['urlRule'] = array(
    'type' => 2,
    'path' => array(
        'Api_hl' => '/do/'
    ),
    'staticUrl' => array(

    ),
    'handleClass' => array(
        1 => '\Factory\Uri\DefaultRule',
        2 => '\Factory\Uri\PathInfoRule',
        3 => '\Factory\Uri\RewriteRule'
    )
);
$_config['siteUrl'] = array(
    'default' => 'http://www.uoke.org/',
);
$_config['templateDir'] = 'Tmp/'; //{APP_DIR}/core/{YOUR TEMPLATE DIR NAME}}