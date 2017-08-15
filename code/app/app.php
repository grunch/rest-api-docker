<?php

require_once BASE_DIR . '/app/bootstrap.php';

use Pimple\Container;
use Test\Rest\Provider\YamlConfigServiceProvider;
use Test\Rest\Provider\DoctrineServiceProvider;

$container = new Container();
$container->register(new YamlConfigServiceProvider(__DIR__ . '/config/config.yml'));
$container->register(new DoctrineServiceProvider(), array(
    'dbs.options' => array(
        'db' => array(
            'driver'   => $container['config']['database']['driver'],
            'host'     => $container['config']['database']['host'],
            'dbname'   => $container['config']['database']['dbname'],
            'user'     => $container['config']['database']['user'],
            'password' => $container['config']['database']['password'],
            'charset'  => $container['config']['database']['charset']
        )
    )
));
