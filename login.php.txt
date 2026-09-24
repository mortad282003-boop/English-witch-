<?php
session_start();
require 'config.php';

if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, name, password, status FROM students WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        if ($row['status'] === 'banned') {
            $error = "🚫 تم حظر حسابك. يرجى مراجعة الإدارة.";
        } else {
            if ($password === $row['password']) {
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['user_name'] = $row['name'];
                header("Location: dashboard.php");
                exit();
            } else {
                $error = "❌ كلمة المرور غير صحيحة.";
            }
        }
    } else {
        $error = "❌ هذا الحساب غير موجود. تأكد من الإيميل أو اشترك أولاً.";
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل دخول الطلاب - English Witch</title>
    <style>
        :root { --primary: #2c4c65; --accent: #e51b23; --bg: #f4f6f9; --white: #ffffff; }
        * { box-sizing: border-box; font-family: 'Segoe UI', Tahoma, sans-serif; }
        body { background: var(--primary); display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; padding: 20px; }
        
        .login-card { background: var(--white); padding: 30px; border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.2); width: 100%; max-width: 400px; text-align: center; border-bottom: 5px solid var(--accent); }
        .login-card h2 { color: var(--primary); margin-bottom: 5px; font-family: serif; font-size: 26px; }
        .login-card p { color: #64748b; margin-bottom: 25px; font-size: 14px; }
        
        .alert { background: #fef2f2; color: #dc2626; padding: 12px; border-radius: 8px; border: 1px solid #fecaca; margin-bottom: 20px; font-weight: bold; font-size: 14px; }
        
        label { display: block; text-align: right; margin-bottom: 8px; color: var(--primary); font-weight: bold; font-size: 14px; }
        input { width: 100%; padding: 12px; margin-bottom: 20px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 15px; direction: ltr; text-align: left; }
        input:focus { outline: none; border-color: var(--primary); }
        
        .btn-submit { background: var(--accent); color: var(--white); padding: 14px; border: none; border-radius: 8px; font-size: 16px; font-weight: bold; cursor: pointer; width: 100%; transition: 0.3s; margin-bottom: 15px; }
        .btn-submit:hover { background: #c0151d; }
        
        .links { font-size: 13px; }
        .links a { color: var(--primary); text-decoration: none; font-weight: bold; }
        .links a:hover { text-decoration: underline; }
    </style>
</head>
<body>

    <div class="login-card">
        <h2>English Witch</h2>
        <p>تسجيل دخول الطلاب 🧙‍♂️</p>
        
        <?php if(!empty($error)) echo "<div class='alert'>$error</div>"; ?>
        
        <form method="POST">
            <label>البريد الإلكتروني (الإيميل):</label>
            <input type="email" name="email" placeholder="01xxxxxxxx@englishwitch.com" required>
            
            <label>كلمة المرور:</label>
            <input type="password" name="password" placeholder="123456" required>
            
            <button type="submit" class="btn-submit">تسجيل الدخول 🚀</button>
        </form>
        
        <div class="links">
            <a href="index.php">الرئيسية</a> | 
            <a href="subscribe.php" style="color: var(--accent);">اشترك الآن</a>
        </div>
    </div>

</body>
</html>
