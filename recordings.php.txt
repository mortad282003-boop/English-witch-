<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$student_id = $_SESSION['user_id'];

// جلب الكورس المسجل فيه الطالب
$enroll_query = $conn->query("SELECT course_id FROM enrollments WHERE student_id = $student_id LIMIT 1");
if ($enroll_query->num_rows == 0) {
    echo "غير مسجل في أي كورس.";
    exit();
}
$course_id = $enroll_query->fetch_assoc()['course_id'];

// جلب تسجيلات اليوتيوب والفيديوهات الخاصة بالكورس
$recordings = $conn->query("SELECT * FROM course_materials WHERE course_id = $course_id AND material_type = 'youtube'");
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>المحاضرات المسجلة - English Witch</title>
    <style>
        :root { --primary: #2c4c65; --accent: #e51b23; --bg: #f4f6f9; --white: #ffffff; }
        body { font-family: 'Segoe UI', Tahoma, sans-serif; background: var(--bg); margin: 0; padding: 20px; }
        .container { max-width: 800px; margin: auto; }
        .card { background: var(--white); padding: 25px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); margin-bottom: 20px; }
        .card h3 { color: var(--primary); margin-top: 0; }
        .video-box { background: #f8fafc; border: 1px solid #e2e8f0; padding: 15px; border-radius: 8px; margin-bottom: 15px; display: flex; justify-content: space-between; align-items: center; }
        .btn { background: var(--accent); color: white; padding: 8px 15px; border-radius: 6px; text-decoration: none; font-size: 14px; font-weight: bold; }
        .btn:hover { background: #c0151d; }
    </style>
</head>
<body>

    <div class="container">
        <div class="card">
            <h3>🎥 المحاضرات والتسجيلات المسجلة</h3>
            <p style="color: #64748b;">هنا يمكنك مشاهدة جميع المحاضرات السابقة لمراجعتها مدى الحياة.</p>
            <a href="dashboard.php" class="btn" style="background: var(--primary);">العودة لوحة التحكم 🔙</a>
        </div>

        <div class="card">
            <h3>📺 القائمة المتاحة</h3>
            <?php 
            if ($recordings && $recordings->num_rows > 0) {
                while ($rec = $recordings->fetch_assoc()) {
                    echo "<div class='video-box'>";
                    echo "<div><strong>📺 " . htmlspecialchars($rec['title']) . "</strong></div>";
                    echo "<a href='" . htmlspecialchars($rec['material_link']) . "' target='_blank' class='btn'>مشاهدة المحاضرة ▶️</a>";
                    echo "</div>";
                }
            } else {
                echo "<p style='text-align: center; color: #64748b;'>لا توجد محاضرات مسجلة حالياً.</p>";
            }
            ?>
        </div>
    </div>

</body>
</html>
