<?php
session_start();
require 'config.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // بيانات المدير الافتراضية (admin / admin123)
    if ($username === 'admin' && $password === 'admin123') {
        $_SESSION['admin_id'] = 1;
        $_SESSION['admin_name'] = 'Admin';
        header("Location: admin_dashboard.php");
        exit();
    } else {
        $error = "❌ اسم المستخدم أو كلمة المرور غير صحيحة.";
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل دخول المدير - English Witch</title>
    <style>
        :root { --primary: #2c4c65; --accent: #e51b23; --bg: #f4f6f9; --white: #ffffff; }
        body { font-family: 'Segoe UI', Tahoma, sans-serif; background: var(--primary); display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        
        .login-card { background: var(--white); padding: 40px; border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.2); width: 100%; max-width: 400px; text-align: center; border-bottom: 5px solid var(--accent); }
        .login-card h2 { color: var(--primary); margin-bottom: 5px; font-family: serif; font-size: 28px; }
        .login-card p { color: #64748b; margin-bottom: 25px; }
        
        .alert { background: #fef2f2; color: #dc2626; padding: 12px; border-radius: 8px; border: 1px solid #fecaca; margin-bottom: 20px; font-weight: bold; }
        
        label { display: block; text-align: right; margin-bottom: 8px; color: var(--primary); font-weight: bold; }
        input { width: 100%; padding: 12px; margin-bottom: 20px; border: 1px solid #cbd5e1; border-radius: 8px; box-sizing: border-box; font-size: 16px; }
        input:focus { outline: none; border-color: var(--primary); }
        
        .btn-submit { background: var(--primary); color: var(--white); padding: 15px; border: none; border-radius: 8px; font-size: 18px; font-weight: bold; cursor: pointer; width: 100%; transition: 0.3s; }
        .btn-submit:hover { background: #1e3649; }
        
        .back-link { display: block; margin-top: 15px; color: #64748b; text-decoration: none; font-size: 14px; }
        .back-link:hover { color: var(--primary); }
    </style>
</head>
<body>

    <div class="login-card">
        <h2>English Witch</h2>
        <p>لوحة تحكم الإدارة 🔐</p>
        
        <?php if(!empty($error)) echo "<div class='alert'>$error</div>"; ?>
        
        <form method="POST">
            <label>اسم المستخدم:</label>
            <input type="text" name="username" placeholder="admin" required>
            
            <label>كلمة المرور:</label>
            <input type="password" name="password" placeholder="admin123" required>
            
            <button type="submit" class="btn-submit">دخول للوحة الإدارة 🚀</button>
        </form>
        
        <a href="index.php" class="back-link">العودة للرئيسية</a>
    </div>

</body>
</html>
