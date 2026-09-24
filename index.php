<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>English Witch - تعلم الإنجليزية بسحر</title>
    <style>
        :root { --primary: #2c4c65; --accent: #e51b23; --bg: #f4f6f9; --white: #ffffff; }
        body { font-family: 'Segoe UI', Tahoma, sans-serif; margin: 0; padding: 0; background: var(--bg); color: #333; }
        
        /* الشريط العلوي */
        .navbar { background: var(--white); padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .navbar h1 { margin: 0; color: var(--primary); font-family: serif; direction: ltr; }
        .nav-links { display: flex; gap: 15px; align-items: center; }
        .nav-links a { text-decoration: none; color: var(--primary); font-weight: bold; transition: 0.3s; }
        .nav-links a:hover { color: var(--accent); }
        .btn-login { background: var(--primary); color: var(--white) !important; padding: 10px 20px; border-radius: 8px; }
        
        /* القسم الرئيسي (الهيرو) */
        .hero { background: var(--primary); color: var(--white); text-align: center; padding: 80px 20px; border-bottom: 5px solid var(--accent); }
        .hero h2 { font-size: 36px; margin-bottom: 15px; }
        .hero p { font-size: 18px; max-width: 600px; margin: 0 auto 30px auto; line-height: 1.6; color: #cbd5e1; }
        .btn-main { background: var(--accent); color: var(--white); padding: 15px 30px; text-decoration: none; font-size: 18px; font-weight: bold; border-radius: 8px; transition: 0.3s; display: inline-block; }
        .btn-main:hover { background: #c0151d; transform: translateY(-3px); }

        /* قسم الخدمات */
        .container { max-width: 1100px; margin: 50px auto; padding: 0 20px; }
        .section-title { text-align: center; color: var(--primary); font-size: 28px; margin-bottom: 40px; }
        
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 30px; }
        .card { background: var(--white); padding: 30px; border-radius: 12px; text-align: center; box-shadow: 0 5px 15px rgba(0,0,0,0.05); border: 1px solid #e2e8f0; transition: 0.3s; }
        .card:hover { transform: translateY(-10px); box-shadow: 0 10px 25px rgba(0,0,0,0.1); border-color: var(--primary); }
        .card-icon { font-size: 50px; margin-bottom: 15px; }
        .card h3 { color: var(--primary); margin-bottom: 15px; }
        .card p { color: #64748b; line-height: 1.6; margin-bottom: 20px; }
        
        .btn-outline { display: inline-block; padding: 10px 20px; border: 2px solid var(--primary); color: var(--primary) !important; text-decoration: none; font-weight: bold; border-radius: 8px; transition: 0.3s; }
        .btn-outline:hover { background: var(--primary); color: var(--white) !important; }
    </style>
</head>
<body>

    <!-- الشريط العلوي -->
    <div class="navbar">
        <h1>English Witch</h1>
        <div class="nav-links">
            <a href="admin_login.php" style="color: #64748b; font-size: 14px;">الإدارة</a>
            <a href="login.php" class="btn-login">دخول الطلاب</a>
        </div>
    </div>

    <!-- واجهة الترحيب -->
    <div class="hero">
        <h2>تعلم الإنجليزية بطريقة ساحرة ومختلفة ✨</h2>
        <p>منصة English Witch توفر لك كورسات تفاعلية، بث مباشر، ومتابعة مستمرة لضمان وصولك للاحترافية بكل سهولة. ابدأ رحلتك الآن!</p>
        <a href="#placement-test" class="btn-main">امتحن تحديد المستوى مجاناً</a>
    </div>

    <!-- الكورسات والاختبارات -->
    <div class="container">
        <h2 class="section-title">ابدأ رحلتك معنا</h2>
        
        <div class="grid">
            <!-- كارت تحديد المستوى -->
            <div class="card" id="placement-test" style="border-top: 4px solid var(--accent);">
                <div class="card-icon">🎯</div>
                <h3>تحديد المستوى (مجاني)</h3>
                <p>اعرف مستواك الحقيقي في اللغة الإنجليزية من خلال اختبارنا السريع والدقيق.</p>
                <a href="free_test.php" class="btn-main" style="padding: 10px 20px; font-size: 16px;">ابدأ الاختبار الآن</a>
            </div>

            <!-- كارت استكشاف الكورسات -->
            <div class="card">
                <div class="card-icon">📚</div>
                <h3>الكورسات المتاحة</h3>
                <p>تصفح كورساتنا، شاهد عينات مجانية من الشرح، واختر الكورس المناسب لمستواك.</p>
                <a href="courses_preview.php" class="btn-outline">استكشاف الكورسات</a>
            </div>

            <!-- كارت الاشتراك والدفع -->
            <div class="card">
                <div class="card-icon">💳</div>
                <h3>الاشتراك والدفع</h3>
                <p>عجبك الشرح؟ اشترك الآن وادفع بكل سهولة عبر (InstaPay، فودافون كاش، أو بنك الخرطوم).</p>
                <a href="subscribe.php" class="btn-outline">طرق الدفع والاشتراك</a>
            </div>
        </div>
    </div>

</body>
</html>
