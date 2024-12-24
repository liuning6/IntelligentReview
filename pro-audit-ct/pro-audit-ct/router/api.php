<?php
// api.php

// ������󷽷�
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // ��ȡ URL �еĲ���
    $name = isset($_GET['name']) ? $_GET['name'] : 'Guest';
    
    // ���� JSON ��Ӧ
    header('Content-Type: application/json');
    echo json_encode(['message' => 'Hello, ' . htmlspecialchars($name)]);
} else {
    // ������󷽷����� GET���򷵻ش���
    header('HTTP/1.1 405 Method Not Allowed');
    echo json_encode(['error' => 'Method Not Allowed']);
}
?>
