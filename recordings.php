<?php
session_start();
require 'config.php';

// حماية الصفحة والتأكد من جلسة الطالب
if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit();
}

$student_id = $_SESSION['student_id'];
$student_name = $_SESSION['student_name'] ?? 'طالب مميز';

// جلب الكورس المشترك فيه الطالب
$stmt_enroll = $conn->prepare("SELECT e.*, c.title AS course_title FROM enrollments e JOIN courses c ON e.course_id = c.id WHERE e.student_id = ? LIMIT 1");
$stmt_enroll->execute([$student_id]);
$enrollment = $stmt_enroll->fetch(PDO::FETCH_ASSOC);

$recordings = [];
if ($enrollment) {
    $course_id = $enrollment['course_id'];
    
    // جلب الفيديوهات والتسجيلات الخاصة بالكورس (نستخدم جدول course_materials أو جدول recordings لو موجود)
    // هنا بنجلب الفيديوهات من جدول course_materials التي نوعها فيديو أو تسجيل
    $stmt_rec = $conn->prepare("SELECT * FROM course_materials WHERE course_id = ? AND (material_type LIKE '%فيديو%' OR material_type LIKE '%تسجيل%' OR material_type LIKE '%youtube%' OR material_type LIKE '%video%') ORDER BY id DESC");
    $stmt_rec->execute([$course_id]);
    $recordings = $stmt_rec->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>المحاضرات المسجلة - English Witch</title>
    <style>
        :root { --primary: #2c4c65; --accent: #e51b23; --bg: #f4f6f9; --white: #ffffff; --text-gray: #64748b; }
        * { box-sizing: border-box; font-family: 'Segoe UI', Tahoma, sans-serif; }
        body { background: var(--bg); margin: 0; padding: 0; }
        .header { background: var(--primary); color: var(--white); padding: 20px; text-align: center; border-bottom: 5px solid var(--accent); }
        .container { padding: 20px; max-width: 900px; margin: auto; }
        
        .card { background: var(--white); padding: 25px; border-radius: 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); border: 1px solid #e2e8f0; margin-bottom: 20px; }
        .card h3 { color: var(--primary); border-bottom: 2px solid var(--bg); padding-bottom: 10px; margin-top: 0; }
        
        .video-item { padding: 15px; border-bottom: 1px solid #e2e8f0; margin-bottom: 15px; }
        .video-item:last-child { border-bottom: none; margin-bottom: 0; }
        .video-title { font-weight: bold; color: var(--primary); font-size: 16px; margin-bottom: 10px; display: block; }
        
        .btn { display: inline-block; background: var(--accent); color: var(--white); padding: 10px 20px; text-decoration: none; border-radius: 8px; font-weight: bold; font-size: 14px; transition: 0.3s; }
        .btn:hover { background: #c0151d; }
        
        .back-link { display: block; text-align: center; margin-top: 20px; text-decoration: none; color: var(--text-gray); font-weight: bold; }
        .back-link:hover { color: var(--primary); }
    </style>
</head>
<body>

    <div class="header">
        <h2>English Witch</h2>
        <p>المحاضرات المسجلة والفيديوهات - <?php echo $enrollment ? htmlspecialchars($enrollment['course_title']) : ''; ?></p>
    </div>

    <div class="container">
        <div class="card">
            <h3>🎥 أرشيف المحاضرات المسجلة</h3>
            
            <?php if ($enrollment && count($recordings) > 0): ?>
                <?php foreach ($recordings as $rec): ?>
                    <div class="video-item">
                        <span class="video-title">▶️ <?php echo htmlspecialchars($rec['title']); ?></span>
                        <a href="<?php echo htmlspecialchars($rec['material_link']); ?>" target="_blank" class="btn">مشاهدة المحاضرة 📺</a>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p style="text-align: center; color: var(--text-gray); padding: 20px;">لا توجد محاضرات مسجلة مرفوعة حالياً. ترقب رفع التسجيلات قريباً!</p>
            <?php endif; ?>
        </div>

        <a href="dashboard.php" class="back-link">← العودة إلى لوحة التحكم الرئيسية</a>
    </div>

</body>
</html>
