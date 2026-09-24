<?php
session_start();
require 'config.php';

$msg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $login_input = trim($_POST['login_input']); // رقم الهاتف أو البريد
    $password = trim($_POST['password']);

    // البحث عن الطالب باستخدام PDO الصحيح
    $stmt = $conn->prepare("SELECT * FROM students WHERE email = ? OR phone = ?");
    $stmt->execute([$login_input, $login_input]);
    $student = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($student) {
        if ($student['status'] === 'banned') {
            $msg = "<div style='background: #ef4444; color: white; padding: 12px; border-radius: 8px; text-align: center; font-weight: bold; margin-bottom: 15px;'>⛔ عذراً، تم حظر هذا الحساب. يرجى التواصل مع الإدارة.</div>";
        } elseif ($password === $student['password']) {
            $_SESSION['student_id'] = $student['id'];
            $_SESSION['student_name'] = $student['name'];
            header("Location: dashboard.php");
            exit();
        } else {
            $msg = "<div style='background: #ef4444; color: white; padding: 12px; border-radius: 8px; text-align: center; font-weight: bold; margin-bottom: 15px;'>❌ كلمة المرور غير صحيحة.</div>";
        }
    } else {
        $msg = "<div style='background: #ef4444; color: white; padding: 12px; border-radius: 8px; text-align: center; font-weight: bold; margin-bottom: 15px;'>❌ البيانات المدخلة غير مسجلة لدينا. يرجى الاشتراك أولاً.</div>";
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
        body { font-family: 'Segoe UI', Tahoma, sans-serif; margin: 0; padding: 0; background: var(--bg); color: #333; display: flex; justify-content: center; align-items: center; height: 100vh; }
        .card { background: var(--white); padding: 40px; border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.08); width: 100%; max-width: 400px; border: 1px solid #e2e8f0; }
        .card h2 { color: var(--primary); margin-top: 0; text-align: center; margin-bottom: 25px; font-family: serif; }
        label { display: block; margin-bottom: 6px; color: var(--primary); font-weight: bold; font-size: 14px; }
        input { width: 100%; padding: 12px; margin-bottom: 20px; border: 1px solid #cbd5e1; border-radius: 8px; box-sizing: border-box; font-size: 15px; }
        .btn-main { background: var(--primary); color: var(--white); width: 100%; padding: 12px; border: none; font-size: 16px; font-weight: bold; border-radius: 8px; cursor: pointer; transition: 0.3s; }
        .btn-main:hover { background: #1e3547; }
        .back-link { display: block; text-align: center; margin-top: 20px; text-decoration: none; color: #64748b; font-size: 14px; font-weight: bold; }
        .back-link:hover { color: var(--primary); }
    </style>
</head>
<body>

    <div class="card">
        <h2>🎓 دخول الطلاب</h2>
        <?php echo $msg; ?>
        <form method="POST">
            <label>رقم الهاتف أو البريد الإلكتروني:</label>
            <input type="text" name="login_input" placeholder="مثال: 0912345678" required>
            
            <label>كلمة المرور:</label>
            <input type="password" name="password" placeholder="أدخل كلمة المرور..." required>

            <button type="submit" class="btn-main">تسجيل الدخول 🚀</button>
        </form>
        <a href="index.php" class="back-link">← العودة للصفحة الرئيسية</a>
    </div>

</body>
</html>
