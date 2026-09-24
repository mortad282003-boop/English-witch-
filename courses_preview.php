<?php
session_start();
require 'config.php';

// جلب الكورسات المضافة من قبل الأدمن
try {
    $stmt = $conn->query("SELECT * FROM courses ORDER BY id DESC");
    $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $courses = [];
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>الكورسات المتاحة - English Witch</title>
    <style>
        :root { --primary: #2c4c65; --accent: #e51b23; --bg: #f4f6f9; --white: #ffffff; }
        body { font-family: 'Segoe UI', Tahoma, sans-serif; margin: 0; padding: 0; background: var(--bg); color: #333; }
        
        .navbar { background: var(--white); padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .navbar h1 { margin: 0; color: var(--primary); font-family: serif; }
        .navbar a { text-decoration: none; color: var(--primary); font-weight: bold; }
        
        .container { max-width: 1100px; margin: 40px auto; padding: 0 20px; }
        .section-title { text-align: center; color: var(--primary); font-size: 28px; margin-bottom: 30px; }
        
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 25px; }
        .card { background: var(--white); padding: 25px; border-radius: 12px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); border: 1px solid #e2e8f0; transition: 0.3s; text-align: center; }
        .card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.1); }
        .card-icon { font-size: 45px; margin-bottom: 15px; }
        .card h3 { color: var(--primary); margin-bottom: 10px; font-size: 20px; }
        .card p { color: #64748b; line-height: 1.6; margin-bottom: 20px; font-size: 14px; }
        
        .btn-subscribe { background: var(--accent); color: white; padding: 10px 20px; border-radius: 6px; text-decoration: none; font-weight: bold; display: inline-block; }
        .btn-subscribe:hover { background: #c0151d; }
        
        .no-courses { text-align: center; color: #64748b; font-size: 16px; grid-column: 1 / -1; padding: 40px; background: white; border-radius: 12px; }
        
        .back-home { display: block; width: fit-content; margin: 30px auto 0; text-decoration: none; color: var(--primary); font-weight: bold; }
    </style>
</head>
<body>

    <div class="navbar">
        <h1>English Witch</h1>
        <a href="index.php">الرئيسية 🏠</a>
    </div>

    <div class="container">
        <h2 class="section-title">📚 الكورسات التعليمية المتاحة</h2>
        
        <div class="grid">
            <?php if (!empty($courses)): ?>
                <?php foreach ($courses as $course): ?>
                    <div class="card">
                        <div class="card-icon"><?php echo htmlspecialchars($course['icon'] ?? '📖'); ?></div>
                        <h3><?php echo htmlspecialchars($course['title']); ?></h3>
                        <p><?php echo nl2br(htmlspecialchars($course['description'])); ?></p>
                        <a href="subscribe.php?course_id=<?php echo $course['id']; ?>" class="btn-subscribe">اشترك الآن 🚀</a>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-courses">لا توجد كورسات مضافة حالياً. يرجى العودة لاحقاً أو إضافة كورسات من لوحة التحكم.</div>
            <?php endif; ?>
        </div>

        <a href="index.php" class="back-home">← العودة للصفحة الرئيسية</a>
    </div>

</body>
</html>
