<?php
/**
 * @author Knowsee
 */
$_config['cache']['memcached'] = array(
    0 => array('hosts' => '127.0.0.1', 'port' => 11211, 'weight' => 0)
);
$_config['cache']['redis'] = array(
    'global' => array('hosts' => 'localhost', 'port' => 6379, 'pre' => 'dptest11')
);
$_config['cache']['file'] = array(
    'cacheDir' => 'data/cache/sql/', 'cacheLife' => 3600
);
$_config['cache']['sql'] = 'file';