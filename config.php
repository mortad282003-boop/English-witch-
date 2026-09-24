<?php
error_reporting(E_ALL);
// إخفاء الأخطاء المزعجة على الشاشة في الإنتاج
ini_set('display_errors', 0);

$host = 'yamabiko.proxy.rlwy.net';
$user = 'root';
$pass = 'FJMtllwHMAvVsWblAUTcMHoLdvTlnQPk'; 
$db   = 'railway';
$port = '28401';

try {
    $dsn = "mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4";
    $conn = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_TIMEOUT => 10,
        PDO::ATTR_PERSISTENT => false
    ]);
} catch (PDOException $e) {
    // عرض خطأ مبسط لو فشل الاتصال
    die("خطأ في الاتصال بقاعدة البيانات. يجدر التحقق من إعدادات السيرفر.");
}
?>
