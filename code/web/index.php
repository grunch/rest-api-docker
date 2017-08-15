<?php
use Test\Rest\Lib\TestAPI;

define('BASE_DIR', realpath(__DIR__ . '/..'));
require_once BASE_DIR . '/app/app.php';

try {
    $api = new TestAPI($container);
    echo $api->processAPI();
} catch (Exception $e) {
    echo json_encode(Array('error' => $e->getMessage()));
}
