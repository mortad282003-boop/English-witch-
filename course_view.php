<?php
session_start();
require 'config.php';

// حماية الصفحة
if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit();
}

$student_id = $_SESSION['student_id'];
$student_name = $_SESSION['student_name'];

// جلب الكورس المشترك فيه الطالب
$stmt_enroll = $conn->prepare("SELECT e.*, c.title AS course_title FROM enrollments e JOIN courses c ON e.course_id = c.id WHERE e.student_id = ? LIMIT 1");
$stmt_enroll->execute([$student_id]);
$enrollment = $stmt_enroll->fetch(PDO::FETCH_ASSOC);

$materials = [];
if ($enrollment) {
    $course_id = $enrollment['course_id'];
    // جلب ملفات ودروس الكورس
    $stmt_mat = $conn->prepare("SELECT * FROM course_materials WHERE course_id = ? ORDER BY id DESC");
    $stmt_mat->execute([$course_id]);
    $materials = $stmt_mat->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>محتوى الكورس - English Witch</title>
    <style>
        :root { --primary: #2c4c65; --accent: #e51b23; --bg: #f4f6f9; --white: #ffffff; --text-gray: #64748b; }
        * { box-sizing: border-box; font-family: 'Segoe UI', Tahoma, sans-serif; }
        body { background: var(--bg); margin: 0; padding: 0; }
        .header { background: var(--primary); color: var(--white); padding: 20px; text-align: center; border-bottom: 5px solid var(--accent); }
        .container { padding: 20px; max-width: 900px; margin: auto; }
        
        .card { background: var(--white); padding: 25px; border-radius: 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); border: 1px solid #e2e8f0; margin-bottom: 20px; }
        .card h3 { color: var(--primary); border-bottom: 2px solid var(--bg); padding-bottom: 10px; margin-top: 0; }
        
        .material-item { display: flex; justify-content: space-between; align-items: center; padding: 15px; border-bottom: 1px solid #e2e8f0; }
        .material-item:last-child { border-bottom: none; }
        .material-title { font-weight: bold; color: var(--primary); font-size: 16px; }
        .material-type { background: #e0f2fe; color: #0369a1; padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: bold; }
        
        .btn { display: inline-block; background: var(--primary); color: var(--white); padding: 8px 16px; text-decoration: none; border-radius: 6px; font-weight: bold; font-size: 14px; transition: 0.3s; }
        .btn:hover { background: #1e3649; }
        
        .back-link { display: block; text-align: center; margin-top: 20px; text-decoration: none; color: var(--text-gray); font-weight: bold; }
        .back-link:hover { color: var(--primary); }
    </style>
</head>
<body>

    <div class="header">
        <h2>English Witch</h2>
        <p>محتوى كورس: <?php echo $enrollment ? htmlspecialchars($enrollment['course_title']) : 'غير مسجل'; ?></p>
    </div>

    <div class="container">
        <div class="card">
            <h3>📁 ملفات ودروس الكورس</h3>
            
            <?php if ($enrollment && count($materials) > 0): ?>
                <?php foreach ($materials as $mat): ?>
                    <div class="material-item">
                        <div>
                            <span class="material-title"><?php echo htmlspecialchars($mat['title']); ?></span>
                            <br><small style="color: var(--text-gray);"><?php echo htmlspecialchars($mat['material_type']); ?></small>
                        </div>
                        <div>
                            <span class="material-type"><?php echo htmlspecialchars($mat['material_type']); ?></span>
                            <a href="<?php echo htmlspecialchars($mat['material_link']); ?>" target="_blank" class="btn" style="margin-right: 10px;">فتح الدرس 🔗</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p style="text-align: center; color: var(--text-gray); padding: 20px;">لا توجد ملفات أو دروس مرفوعة لهذا الكورس حتى الآن. يرجى مراجعة الإدارة قريباً.</p>
            <?php endif; ?>
        </div>

        <a href="dashboard.php" class="back-link">← العودة إلى لوحة التحكم الرئيسية</a>
    </div>

</body>
</html>
