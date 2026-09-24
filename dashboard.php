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

// 1. جلب بيانات الكورس المسجل فيه الطالب باستخدام PDO
$stmt_enroll = $conn->prepare("SELECT e.*, c.title, c.icon FROM enrollments e JOIN courses c ON e.course_id = c.id WHERE e.student_id = ? LIMIT 1");
$stmt_enroll->execute([$student_id]);
$enrollment = $stmt_enroll->fetch(PDO::FETCH_ASSOC);

// 2. التحقق من وجود بث مباشر (Live Zoom) نشط واختبار تحديد المستوى
$zoom_alert = "";
$exam_section = "";
if ($enrollment) {
    $course_id = $enrollment['course_id'];
    
    // فحص الزوم الشغال والتأكد من جلب الرابط بدقة
    $stmt_live = $conn->prepare("SELECT zoom_link FROM live_sessions WHERE course_id = ? AND is_active = 1 LIMIT 1");
    $stmt_live->execute([$course_id]);
    $live = $stmt_live->fetch(PDO::FETCH_ASSOC);
    
    if ($live && !empty($live['zoom_link'])) {
        $zoom_link = $live['zoom_link'];
        $zoom_alert = "
        <div style='background: #fee2e2; border: 2px solid #ef4444; color: #b91c1c; padding: 20px; border-radius: 12px; font-weight: bold; text-align: center; margin-bottom: 25px; box-shadow: 0 4px 15px rgba(239, 68, 68, 0.2); animation: pulse 1.5s infinite;'>
            <div style='font-size: 18px; margin-bottom: 8px;'>🔴 المحاضرة تعمل الآن مباشر (Live Zoom)</div>
            <a href='{$zoom_link}' target='_blank' style='display: inline-block; background: #ef4444; color: white; padding: 10px 25px; border-radius: 8px; text-decoration: none; margin-top: 5px; font-size: 16px; transition: 0.3s;'>انضم لغرفة الزوم الآن 🚀</a>
        </div>";
    }

    // جلب اختبار تحديد المستوى الخاص بالكورس (إن وجد)
    $stmt_exam = $conn->prepare("SELECT * FROM exams WHERE course_id = ? ORDER BY id DESC LIMIT 1");
    $stmt_exam->execute([$course_id]);
    $exam = $stmt_exam->fetch(PDO::FETCH_ASSOC);
    
    if ($exam) {
        $exam_link = $exam['exam_link'];
        $exam_title = $exam['exam_title'];
        $exam_section = "
        <div class='card' style='background: #f0fdf4; border-color: #bbf7d0; margin-top: 20px;'>
            <h3 style='color: #16a34a; border-bottom: 2px solid #dcfce7;'>📝 اختبار وتحديد المستوى</h3>
            <p style='color: #374151; font-size: 14px;'>{$exam_title}</p>
            <a href='{$exam_link}' target='_blank' class='btn' style='background: #16a34a;'>الانتقال للاختبار 🎯</a>
        </div>";
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لوحة الطالب - English Witch</title>
    <style>
        :root { --primary: #2c4c65; --accent: #e51b23; --bg: #f4f6f9; --white: #ffffff; --text-gray: #64748b; }
        * { box-sizing: border-box; font-family: 'Segoe UI', Tahoma, sans-serif; }
        body { background: var(--bg); margin: 0; padding: 0; }
        .header { background: var(--primary); color: var(--white); padding: 20px; text-align: center; border-bottom: 5px solid var(--accent); }
        .container { padding: 20px; max-width: 900px; margin: auto; }
        
        .grid-layout { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px; margin-bottom: 20px; }
        .card { background: var(--white); padding: 25px; border-radius: 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); border: 1px solid #e2e8f0; }
        .card h3 { color: var(--primary); border-bottom: 2px solid var(--bg); padding-bottom: 10px; margin-top: 0; display: flex; justify-content: space-between; font-size: 17px; }
        
        .info-row { display: flex; justify-content: space-between; margin-bottom: 15px; border-bottom: 1px dashed #e2e8f0; padding-bottom: 10px; font-size: 14px; }
        .info-row span:first-child { color: var(--text-gray); font-weight: bold; }
        .info-row span:last-child { color: var(--primary); font-weight: bold; }
        
        .btn { display: block; width: 100%; text-align: center; background: var(--primary); color: var(--white); padding: 12px; text-decoration: none; border-radius: 8px; font-weight: bold; margin-top: 15px; transition: 0.3s; font-size: 15px; }
        .btn:hover { background: #1e3649; }
        
        @keyframes pulse { 0% { transform: scale(1); } 50% { transform: scale(1.02); } 100% { transform: scale(1); } }
    </style>
</head>
<body>

    <div class="header">
        <h2>English Witch</h2>
        <p>مرحباً بك يا <?php echo htmlspecialchars($student_name); ?> 👋</p>
    </div>

    <div class="container">
        
        <!-- قسم التنبيه ومباشر الزوم -->
        <?php echo $zoom_alert; ?>

        <?php if ($enrollment): ?>
        <div class="grid-layout">
            <!-- كارت الكورس وتفاصيل الاشتراك -->
            <div class="card">
                <h3>📚 كورس: <?php echo htmlspecialchars($enrollment['title']); ?> <span style="background: #dcfce7; color: #16a34a; font-size: 12px; padding: 3px 8px; border-radius: 15px;">نشط</span></h3>
                <div class="info-row"><span>نظام الدفع:</span> <span><?php echo htmlspecialchars($enrollment['payment_type']); ?></span></div>
                <div class="info-row"><span>حالة الحساب:</span> <span style="color: #10b981;">تم التفعيل بنجاح ✅</span></div>
            </div>

            <!-- كارت محتوى الدروس والملفات -->
            <div class="card">
                <h3>📁 محتوى الكورس</h3>
                <p style="color: var(--text-gray); font-size: 13px; line-height: 1.5;">استعرض الملفات والدروس التعليمية المرفوعة من الإدارة.</p>
                <a href="course_view.php" class="btn" style="background: #10b981;">فتح محتوى الكورس 🚀</a>
            </div>

            <!-- كارت التسجيلات والمحاضرات المسجلة -->
            <div class="card" style="grid-column: 1 / -1;">
                <h3>🎥 المحاضرات المسجلة</h3>
                <p style="color: var(--text-gray); font-size: 13px;">راجع المحاضرات السابقة وفيديوهات اليوتيوب متى ما شئت.</p>
                <a href="recordings.php" class="btn" style="background: var(--accent);">عرض التسجيلات والمحاضرات ▶️</a>
            </div>
        </div>

        <!-- قسم اختبار تحديد المستوى (إن وجد) -->
        <?php echo $exam_section; ?>

        <?php else: ?>
            <div class="card" style="text-align: center;">
                <h3 style="justify-content: center;">⚠️ لا توجد كورسات مسجلة</h3>
                <p style="color: var(--text-gray);">أنت غير مسجل في أي كورس حالياً. يرجى الانتظار حتى توافق الإدارة على إيصال الدفع الخاص بك.</p>
            </div>
        <?php endif; ?>
        
        <a href="logout.php" class="btn" style="background: #64748b; max-width: 250px; margin: 30px auto;">تسجيل خروج 🚪</a>
    </div>

</body>
</html>
