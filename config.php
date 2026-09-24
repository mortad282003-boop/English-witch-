<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$host = 'iriguchi.proxy.rlwy.net';
$user = 'root';
$pass = 'DXFNVTlqkOgnhRzVHkaxZFHSoZDarDpa'; 
$db   = 'railway';
$port = '37552';

try {
    $dsn = "mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4";
    $conn = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_TIMEOUT => 15
    ]);
} catch (PDOException $e) {
    die("خطأ في الاتصال بقاعدة البيانات: " . $e->getMessage());
}
?>
