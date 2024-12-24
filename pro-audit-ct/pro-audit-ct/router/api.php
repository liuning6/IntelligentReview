<?php
// api.php

// 检查请求方法
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // 获取 URL 中的参数
    $name = isset($_GET['name']) ? $_GET['name'] : 'Guest';
    
    // 返回 JSON 响应
    header('Content-Type: application/json');
    echo json_encode(['message' => 'Hello, ' . htmlspecialchars($name)]);
} else {
    // 如果请求方法不是 GET，则返回错误
    header('HTTP/1.1 405 Method Not Allowed');
    echo json_encode(['error' => 'Method Not Allowed']);
}
?>
