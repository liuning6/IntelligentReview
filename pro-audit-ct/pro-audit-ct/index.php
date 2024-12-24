<?php

// if($_SERVER['REQUEST_URI'] == '/' && $_SERVER['HTTP_HOST'] == '27.25.149.113:22235'){
// 	header("HTTP/1.1 404 Not Found");
// 	header("Status: 404 Not Found");
// 	exit;
// }

ini_set('memory_limit', '1024M');

date_default_timezone_set('PRC');
// 定义应用目录
define('APP_PATH', __DIR__ . '/application/');
// 加载框架引导文件
require __DIR__ . '/thinkphp/start.php';