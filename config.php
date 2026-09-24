<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// التحقق هل نحن في ريلوي ولا على السيرفر المحلي
if (getenv('MYSQLHOST') || getenv('RAILWAY_STATIC_URL')) {
    // إعدادات سحابة ريلوي
    $host = getenv('MYSQLHOST') ?: 'yamabiko.proxy.rlwy.net';
    $user = getenv('MYSQLUSER') ?: 'root';
    $pass = getenv('MYSQLPASSWORD') ?: 'FJMtllwHMAvVsWblAUTcMHoLdvTlnQPk'; 
    $db   = getenv('MYSQLDATABASE') ?: 'railway';
    $port = getenv('MYSQLPORT') ?: '3306';
} else {
    // إعدادات الاستضافة المحلية (Localhost) الخاصة بك
    $host = 'localhost';
    $user = 'root';
    $pass = ''; // كلمة المرور المحلية غالباً فارغة
    $db   = 'english_witch'; // اسم قاعدة بياناتك المحلية (عدله لو مختلف)
    $port = '3306';
}

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
