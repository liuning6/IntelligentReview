<?php
error_reporting(E_ALL);      // 开启所有级别的错误报告
ini_set('display_errors', 1); // 将错误报告显示在页面上

// 下面是你的数据库连接代码
$servername = "127.0.0.1"; // 数据库服务器
$username = "root";         // 数据库用户名
$password = "123456";       // 数据库密码
$dbname = "2Water";         // 数据库名称

// 创建连接
$conn = new mysqli($servername, $username, $password, $dbname);

// 检查连接
if ($conn->connect_error) {
    die("连接失败: " . $conn->connect_error);
}
echo "连接成功";
$conn->close();
?>
