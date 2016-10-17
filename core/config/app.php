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
$_config['urlRule'] = array(
    'type' => 2,
    'path' => array(
        'test_user' => '/do/id/username/',
        'index_index' => '',
    ),
    'staticUrl' => array(

    ),
    'handleClass' => array(
        1 => '\Factory\Uri\DefaultRule',
        2 => '\Factory\Uri\PathInfoRule',
        3 => '\Factory\Uri\RewriteRule'
    )
);