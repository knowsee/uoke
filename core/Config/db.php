<?php
/**
 * User: chengyi
 * Date: 16/9/12
 * Time: 下午12:27
 */
$_config['db'] = array(
    'host' => 'localhost',
    'user' => 'root',
    'password' => 'root',
    'name' => 'tag',
    'charset' => 'utf8',
    'pre' => '',
    'driver' => 'Mysqli',
    'pconnect' => 0
);
$_config['dbDriver'] = array(
    'Mysqli' => '\\DbExtend\\Mysqli'
);