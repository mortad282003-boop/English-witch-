<?php
require 'config.php';

$msg = "";

// إنشاء مجلد لحفظ صور الإيصالات لو ما موجود
$target_dir = "uploads/";
if (!file_exists($target_dir)) {
    mkdir($target_dir, 0777, true);
}

// كود استقبال البيانات وحفظها في قاعدة البيانات
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_payment'])) {
    $name = $_POST['student_name'];
    $phone = $_POST['phone'];
    $course_id = $_POST['course_id'];
    
    // رفع صورة الإيصال
    $file_name = time() . "_" . basename($_FILES["receipt_image"]["name"]); // تغيير الاسم عشان ما يتكرر
    $target_file = $target_dir . $file_name;
    
    if (move_uploaded_file($_FILES["receipt_image"]["tmp_name"], $target_file)) {
        // حفظ الطلب في قاعدة البيانات
        $stmt = $conn->prepare("INSERT INTO payment_requests (student_name, phone, course_id, receipt_image) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssis", $name, $phone, $course_id, $target_file);
        
        if ($stmt->execute()) {
            $msg = "<div class='alert success'>✅ تم إرسال طلبك بنجاح! سيتم مراجعة الإيصال وإرسال بيانات الدخول عبر الواتساب قريباً.</div>";
        } else {
            $msg = "<div class='alert error'>❌ حدث خطأ في قاعدة البيانات. الرجاء المحاولة مرة أخرى.</div>";
        }
    } else {
        $msg = "<div class='alert error'>❌ فشل رفع الصورة. الرجاء التأكد من حجم الصورة والمحاولة مرة أخرى.</div>";
    }
}

// جلب طرق الدفع المفعلة من قاعدة البيانات
$payment_methods = $conn->query("SELECT * FROM payment_methods WHERE is_active = 1");

// جلب الكورسات المتاحة للاشتراك
$courses = $conn->query("SELECT id, title FROM courses");
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
        
        .header { background: var(--primary); color: var(--white); text-align: center; padding: 30px 20px; border-bottom: 5px solid var(--accent); }
        .header h1 { margin: 0; font-size: 28px; }
        
        .container { max-width: 800px; margin: 40px auto; padding: 0 20px; }
        
        .card { background: var(--white); padding: 30px; border-radius: 12px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); border: 1px solid #e2e8f0; margin-bottom: 25px; }
        .card h3 { color: var(--primary); margin-top: 0; border-bottom: 2px solid var(--bg); padding-bottom: 10px; margin-bottom: 20px; }
        
        .methods-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-bottom: 20px; }
        .method-box { background: #e0e7ff; border: 1px solid #c7d2fe; padding: 15px; border-radius: 8px; text-align: center; }
        .method-box h4 { color: var(--primary); margin: 0 0 10px 0; }
        .method-box p { font-weight: bold; color: var(--accent); margin: 0; font-size: 18px; direction: ltr; }
        
        label { display: block; margin-bottom: 8px; font-weight: bold; color: var(--primary); }
        input, select { width: 100%; padding: 12px; margin-bottom: 20px; border: 1px solid #cbd5e1; border-radius: 8px; box-sizing: border-box; font-size: 15px; }
        input:focus, select:focus { outline: none; border-color: var(--primary); }
        
        .btn-submit { background: var(--accent); color: var(--white); padding: 15px; border: none; border-radius: 8px; font-size: 18px; font-weight: bold; cursor: pointer; width: 100%; transition: 0.3s; }
        .btn-submit:hover { background: #c0151d; }
        
        .alert { padding: 15px; border-radius: 8px; margin-bottom: 20px; font-weight: bold; text-align: center; }
        .success { background: #dcfce7; color: #16a34a; border: 1px solid #bbf7d0; }
        .error { background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; }
        
        .file-upload-wrapper { position: relative; margin-bottom: 20px; }
        .file-upload-wrapper input[type="file"] { padding: 10px; background: #f8fafc; border: 2px dashed #cbd5e1; cursor: pointer; }
    </style>
</head>
<body>

    <div class="header">
        <h1>💳 الاشتراك وتأكيد الدفع</h1>
        <p style="color: #cbd5e1; margin-top: 10px;">اختر الكورس، قم بتحويل الرسوم، وارفع إيصال الدفع هنا</p>
    </div>

    <div class="container">
        <!-- عرض رسائل النجاح أو الخطأ -->
        <?php echo $msg; ?>

        <!-- طرق الدفع المتاحة -->
        <div class="card">
            <h3>🏦 طرق الدفع المتاحة</h3>
            <div class="methods-grid">
                <?php 
                if ($payment_methods && $payment_methods->num_rows > 0) {
                    while ($method = $payment_methods->fetch_assoc()) {
                        echo "<div class='method-box'>";
                        echo "<h4>" . htmlspecialchars($method['method_name']) . "</h4>";
                        echo "<p>" . htmlspecialchars($method['account_details']) . "</p>";
                        echo "</div>";
                    }
                } else {
                    echo "<p style='color: #64748b;'>جاري تحديث طرق الدفع...</p>";
                }
                ?>
            </div>
        </div>

        <!-- فورم رفع الإيصال -->
        <div class="card">
            <h3>📤 رفع إيصال التحويل</h3>
            <!-- أهم حاجة في الفورم ده enctype عشان يقبل رفع الصور -->
            <form method="POST" enctype="multipart/form-data">
                
                <label>الاسم الرباعي:</label>
                <input type="text" name="student_name" placeholder="اكتب اسمك كامل عشان الشهادة" required>
                
                <label>رقم الواتساب:</label>
                <input type="tel" name="phone" placeholder="مثال: 01xxxxxxxxx (للتواصل وإرسال الحساب)" required>
                
                <label>اختر الكورس المراد الاشتراك به:</label>
                <select name="course_id" required>
                    <option value="" disabled selected>-- اختر الكورس --</option>
                    <?php 
                    if ($courses && $courses->num_rows > 0) {
                        while ($course = $courses->fetch_assoc()) {
                            echo "<option value='" . $course['id'] . "'>" . htmlspecialchars($course['title']) . "</option>";
                        }
                    } else {
                        echo "<option value='0'>كورس اللغة الإنجليزية الشامل</option>";
                    }
                    ?>
                </select>
                
                <label>صورة إيصال الدفع (سكرين شوت):</label>
                <div class="file-upload-wrapper">
                    <input type="file" name="receipt_image" accept="image/*" required>
                </div>
                
                <button type="submit" name="submit_payment" class="btn-submit">تأكيد الدفع وإرسال الطلب ✅</button>
            </form>
        </div>
    </div>

</body>
</html>
