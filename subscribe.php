<?php
session_start();
require 'config.php';

// التأكد من وجود جدول الكورسات وجدول طلبات الدفع
$conn->exec("CREATE TABLE IF NOT EXISTS courses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT DEFAULT NULL,
    icon VARCHAR(50) DEFAULT '📖',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

$conn->exec("CREATE TABLE IF NOT EXISTS payment_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_name VARCHAR(150),
    phone VARCHAR(50),
    course_id INT,
    receipt_image TEXT,
    status VARCHAR(50) DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

$course_id = $_GET['course_id'] ?? '';

// جلب الكورسات للقائمة المنسدلة
$courses = $conn->query("SELECT * FROM courses")->fetchAll(PDO::FETCH_ASSOC);

$msg = "";
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_payment'])) {
    $student_name = $_POST['student_name'];
    $phone = $_POST['phone'];
    $selected_course = $_POST['course_id'];
    
    // رفع صورة الإيصال
    $receipt_image = "";
    if (isset($_FILES['receipt']) && $_FILES['receipt']['error'] == 0) {
        $target_dir = "uploads/";
        if (!is_dir($target_dir)) { mkdir($target_dir, 0777, true); }
        $receipt_image = $target_dir . time() . "_" . basename($_FILES['receipt']['name']);
        move_uploaded_file($_FILES['receipt']['tmp_name'], $receipt_image);
    }
    
    $stmt = $conn->prepare("INSERT INTO payment_requests (student_name, phone, course_id, receipt_image, status) VALUES (?, ?, ?, ?, 'pending')");
    $stmt->execute([$student_name, $phone, $selected_course, $receipt_image]);
    
    $msg = "<div style='background: #10b981; color: white; padding: 15px; border-radius: 8px; text-align: center; font-weight: bold; margin-bottom: 20px;'>✅ تم إرسال طلب الاشتراك بنجاح! سيتم مراجعته وتفعيل حسابك قريباً.</div>";
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>الاشتراك والدفع - English Witch</title>
    <style>
        :root { --primary: #2c4c65; --accent: #e51b23; --bg: #f4f6f9; --white: #ffffff; }
        body { font-family: 'Segoe UI', Tahoma, sans-serif; margin: 0; padding: 0; background: var(--bg); color: #333; }
        .navbar { background: var(--white); padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .navbar h1 { margin: 0; color: var(--primary); font-family: serif; }
        .navbar a { text-decoration: none; color: var(--primary); font-weight: bold; }
        .container { max-width: 600px; margin: 40px auto; padding: 0 20px; }
        .card { background: var(--white); padding: 30px; border-radius: 12px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); border: 1px solid #e2e8f0; }
        .card h2 { color: var(--primary); margin-top: 0; text-align: center; margin-bottom: 20px; }
        label { display: block; margin-bottom: 6px; color: var(--primary); font-weight: bold; font-size: 14px; }
        input, select { width: 100%; padding: 12px; margin-bottom: 15px; border: 1px solid #cbd5e1; border-radius: 8px; box-sizing: border-box; font-size: 15px; background: #fff; }
        .btn-main { background: var(--accent); color: var(--white); width: 100%; padding: 12px; border: none; font-size: 16px; font-weight: bold; border-radius: 8px; cursor: pointer; transition: 0.3s; }
        .btn-main:hover { background: #c0151d; }
    </style>
</head>
<body>

    <div class="navbar">
        <h1>English Witch</h1>
        <a href="index.php">الرئيسية 🏠</a>
    </div>

    <div class="container">
        <div class="card">
            <h2>💳 تأكيد الاشتراك وإرسال الإيصال</h2>
            <?php echo $msg; ?>
            <form method="POST" enctype="multipart/form-data">
                <label>اسمك الكامل:</label>
                <input type="text" name="student_name" placeholder="أدخل اسمك هنا..." required>
                
                <label>رقم الهاتف (سيستخدم لتسجيل الدخول):</label>
                <input type="text" name="phone" placeholder="مثال: 0912345678" required>
                
                <label>اختر الكورس:</label>
                <select name="course_id" required>
                    <option value="">-- اختر الكورس المطلوب --</option>
                    <?php foreach($courses as $c): ?>
                        <option value="<?php echo $c['id']; ?>" <?php echo ($course_id == $c['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($c['title']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <label>صورة إيصال التحويل (صورة الشاشة):</label>
                <input type="file" name="receipt" accept="image/*" required>

                <button type="submit" name="submit_payment" class="btn-main">إرسال طلب الاشتراك 🚀</button>
            </form>
        </div>
    </div>

</body>
</html>
