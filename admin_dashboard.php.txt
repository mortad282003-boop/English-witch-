<?php
session_start();
require 'config.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

$msg = "";

// 1. قبول الدفع وتفعيل الطالب
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['approve_payment'])) {
    $req_id = $_POST['req_id'];
    $student_name = $_POST['student_name'];
    $phone = $_POST['phone'];
    $course_id = $_POST['course_id'];
    
    $email = $phone . "@englishwitch.com";
    $password = "123456";
    
    $stmt_check = $conn->prepare("SELECT id FROM students WHERE phone = ? OR email = ?");
    $stmt_check->execute([$phone, $email]);
    $student = $stmt_check->fetch(PDO::FETCH_ASSOC);

    if ($student) {
        $student_id = $student['id'];
    } else {
        $stmt = $conn->prepare("INSERT INTO students (name, email, phone, password, status) VALUES (?, ?, ?, ?, 'active')");
        $stmt->execute([$student_name, $email, $phone, $password]);
        $student_id = $conn->lastInsertId();
    }
    
    $stmt_enroll = $conn->prepare("INSERT INTO enrollments (student_id, course_id, payment_type) VALUES (?, ?, 'كاش')");
    $stmt_enroll->execute([$student_id, $course_id]);

    $stmt_update = $conn->prepare("UPDATE payment_requests SET status = 'approved' WHERE id = ?");
    $stmt_update->execute([$req_id]);

    $msg = "<div style='background: #10b981; color: white; padding: 15px; border-radius: 8px; text-align: center; font-weight: bold; margin-bottom: 20px;'>✅ تم تفعيل حساب الطالب بنجاح!</div>";
}

// 2. إضافة كورس جديد
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_course'])) {
    $title = $_POST['title'];
    $desc = $_POST['description'];
    $icon = $_POST['icon'];
    
    $stmt = $conn->prepare("INSERT INTO courses (title, description, icon) VALUES (?, ?, ?)");
    $stmt->execute([$title, $desc, $icon]);
    $msg = "<div style='background: #10b981; color: white; padding: 15px; border-radius: 8px; text-align: center; font-weight: bold; margin-bottom: 20px;'>✅ تم إضافة الكورس بنجاح!</div>";
}

// 3. إضافة ملف تعليمي
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_material'])) {
    $course_id = $_POST['course_id'];
    $title = $_POST['title'];
    $type = $_POST['material_type'];
    $link = $_POST['material_link'];
    
    $stmt = $conn->prepare("INSERT INTO course_materials (course_id, title, material_type, material_link) VALUES (?, ?, ?, ?)");
    $stmt->execute([$course_id, $title, $type, $link]);
    $msg = "<div style='background: #10b981; color: white; padding: 15px; border-radius: 8px; text-align: center; font-weight: bold; margin-bottom: 20px;'>✅ تم رفع المادة التعليمية بنجاح!</div>";
}

// 4. إضافة رابط امتحان تقييم المستوى
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_exam_link'])) {
    $course_id = $_POST['course_id'];
    $exam_title = $_POST['exam_title'];
    $exam_link = $_POST['exam_link'];
    
    $conn->exec("CREATE TABLE IF NOT EXISTS exams (
        id INT AUTO_INCREMENT PRIMARY KEY,
        course_id INT,
        exam_title VARCHAR(150),
        exam_link TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    
    $stmt = $conn->prepare("INSERT INTO exams (course_id, exam_title, exam_link) VALUES (?, ?, ?)");
    $stmt->execute([$course_id, $exam_title, $exam_link]);
    $msg = "<div style='background: #10b981; color: white; padding: 15px; border-radius: 8px; text-align: center; font-weight: bold; margin-bottom: 20px;'>✅ تم نشر رابط الاختبار بنجاح!</div>";
}

// 5. إدارة الزوم والحظر
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['start_zoom'])) {
    $course_id = $_POST['course_id'];
    $zoom_link = $_POST['zoom_link'];
    $conn->exec("UPDATE live_sessions SET is_active = 0");
    $stmt = $conn->prepare("INSERT INTO live_sessions (course_id, zoom_link, is_active) VALUES (?, ?, 1)");
    $stmt->execute([$course_id, $zoom_link]);
    $msg = "<div style='background: #10b981; color: white; padding: 15px; border-radius: 8px; text-align: center; font-weight: bold; margin-bottom: 20px;'>🔴 تم إطلاق بث الزوم!</div>";
}
if (isset($_POST['stop_zoom'])) {
    $conn->exec("UPDATE live_sessions SET is_active = 0");
    $msg = "<div style='background: #10b981; color: white; padding: 15px; border-radius: 8px; text-align: center; font-weight: bold; margin-bottom: 20px;'>⏹️ تم إيقاف البث.</div>";
}
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['toggle_ban'])) {
    $email = $_POST['student_email'];
    $status = $_POST['action_type'];
    $stmt = $conn->prepare("UPDATE students SET status = ? WHERE email = ?");
    $stmt->execute([$status, $email]);
    $msg = "<div style='background: #10b981; color: white; padding: 15px; border-radius: 8px; text-align: center; font-weight: bold; margin-bottom: 20px;'>✅ تم تحديث حالة الطالب.</div>";
}

$pending_requests = $conn->query("SELECT pr.*, c.title as course_title FROM payment_requests pr LEFT JOIN courses c ON pr.course_id = c.id WHERE pr.status = 'pending'")->fetchAll(PDO::FETCH_ASSOC);
$all_courses = $conn->query("SELECT * FROM courses")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لوحة التحكم الشاملة - English Witch</title>
    <style>
        :root { --primary: #2c4c65; --primary-light: #3a6384; --accent: #e51b23; --bg: #f4f6f9; --white: #ffffff; }
        * { box-sizing: border-box; font-family: 'Segoe UI', Tahoma, sans-serif; }
        body { background: var(--bg); margin: 0; display: flex; min-height: 100vh; overflow-x: hidden; }
        
        .sidebar { width: 260px; background: var(--primary); color: var(--white); padding: 20px 0; flex-shrink: 0; transition: right 0.3s ease; z-index: 1000; height: 100vh; position: sticky; top: 0; overflow-y: auto; }
        .sidebar h2 { text-align: center; margin-bottom: 20px; font-family: serif; font-size: 22px; }
        .nav-links { list-style: none; padding: 0; margin: 0; }
        .nav-links li { padding: 14px 20px; cursor: pointer; border-bottom: 1px solid rgba(255,255,255,0.1); font-weight: bold; font-size: 14px; transition: 0.3s; }
        .nav-links li:hover, .nav-links li.active { background: var(--primary-light); border-right: 4px solid var(--accent); }
        
        .main-content { flex-grow: 1; padding: 20px; overflow-y: auto; width: 100%; }
        
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; background: var(--white); padding: 15px 20px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); border: 1px solid #e2e8f0; }
        .header-title { display: flex; align-items: center; gap: 15px; }
        .header h1 { color: var(--primary); font-size: 18px; margin: 0; }
        .menu-toggle { display: none; font-size: 24px; cursor: pointer; color: var(--primary); background: none; border: none; }
        .overlay { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 999; }

        .tab-content { display: none; }
        .tab-content.active { display: block; animation: fadeIn 0.4s ease-in-out; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        
        .card { background: var(--white); padding: 20px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); margin-bottom: 20px; border: 1px solid #e2e8f0; }
        .card h3 { color: var(--primary); border-bottom: 2px solid var(--bg); padding-bottom: 10px; margin-top: 0; font-size: 17px; }
        
        .table-responsive { width: 100%; overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; min-width: 500px; }
        th, td { padding: 12px; text-align: right; border-bottom: 1px solid #e2e8f0; font-size: 14px; }
        th { background: var(--bg); color: var(--primary); }
        
        label { display: block; margin-bottom: 6px; color: var(--primary); font-weight: bold; font-size: 13px; }
        input, textarea, select { width: 100%; padding: 12px; margin-bottom: 15px; border: 1px solid #cbd5e1; border-radius: 8px; box-sizing: border-box; font-size: 15px; background: #fff; }
        
        .btn { padding: 10px 15px; border: none; border-radius: 6px; cursor: pointer; font-weight: bold; color: white; text-decoration: none; display: inline-block; font-size: 14px; text-align: center; }
        .btn-green { background: #10b981; } .btn-green:hover { background: #059669; }
        .btn-blue { background: #3b82f6; } .btn-blue:hover { background: #2563eb; }
        .btn-red { background: var(--accent); } .btn-red:hover { background: #c0151d; }

        @media (max-width: 768px) {
            .sidebar { position: fixed; right: -260px; top: 0; height: 100vh; }
            .sidebar.active { right: 0; }
            .overlay.active { display: block; }
            .menu-toggle { display: block; }
            .main-content { padding: 15px; }
        }
    </style>
</head>
<body>

    <div class="overlay" onclick="toggleSidebar()"></div>

    <div class="sidebar" id="sidebar">
        <h2>English Witch</h2>
        <ul class="nav-links">
            <li class="active" onclick="openTab('requests', this)">🔔 طلبات الاشتراك</li>
            <li onclick="openTab('courses', this)">📚 إدارة الكورسات</li>
            <li onclick="openTab('materials', this)">📂 رفع الملفات والدروس</li>
            <li onclick="openTab('exams', this)">📝 رابط اختبار المستوى</li>
            <li onclick="openTab('zoom', this)">🔴 بث الزوم المباشر</li>
            <li onclick="openTab('students', this)">🚫 إدارة الطلاب</li>
            <li><a href="admin_login.php" style="color: white; text-decoration: none; display: block;">🚪 تسجيل خروج</a></li>
        </ul>
    </div>

    <div class="main-content">
        <div class="header">
            <div class="header-title">
                <button class="menu-toggle" onclick="toggleSidebar()">☰</button>
                <h1 id="page-title">طلبات الاشتراك</h1>
            </div>
            <div style="font-size: 14px; color: var(--primary);">مدير النظام</div>
        </div>

        <?php echo $msg; ?>
        
        <!-- 1. طلبات الاشتراك -->
        <div id="requests" class="tab-content active">
            <div class="card">
                <h3>🔔 مراجعة إيصالات الدفع وتفعيل الطلاب</h3>
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>اسم الطالب</th>
                                <th>الكورس</th>
                                <th>رقم الهاتف</th>
                                <th>الإيصال</th>
                                <th>الإجراء</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            if (!empty($pending_requests)) {
                                foreach ($pending_requests as $req) {
                                    $whatsapp_msg = urlencode("مرحباً " . $req['student_name'] . "، تم قبول إيصال الدفع وتفعيل حسابك في منصة English Witch.\nرابط الدخول: https://english-witch-production.up.railway.app/login.php\nالإيميل: " . $req['phone'] . "@englishwitch.com\nكلمة المرور: 123456");
                                    
                                    echo "<tr>";
                                    echo "<td>" . htmlspecialchars($req['student_name']) . "</td>";
                                    echo "<td>" . htmlspecialchars($req['course_title'] ?? 'غير محدد') . "</td>";
                                    echo "<td>" . htmlspecialchars($req['phone']) . "</td>";
                                    echo "<td><a href='" . htmlspecialchars($req['receipt_image'] ?? '#') . "' target='_blank' class='btn btn-blue'>الإيصال 🖼️</a></td>";
                                    echo "<td>
                                            <div style='display: flex; gap: 5px; align-items: center;'>
                                                <form method='POST' style='margin:0;'>
                                                    <input type='hidden' name='req_id' value='" . $req['id'] . "'>
                                                    <input type='hidden' name='student_name' value='" . $req['student_name'] . "'>
                                                    <input type='hidden' name='phone' value='" . $req['phone'] . "'>
                                                    <input type='hidden' name='course_id' value='" . $req['course_id'] . "'>
                                                    <button type='submit' name='approve_payment' class='btn btn-green'>تفعيل ✅</button>
                                                </form>
                                                <a href='https://wa.me/2" . $req['phone'] . "?text=" . $whatsapp_msg . "' target='_blank' class='btn' style='background:#25D366;'>واتس 💬</a>
                                            </div>
                                          </td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='5' style='text-align:center; color:#64748b; padding:20px;'>لا توجد طلبات دفع معلقة.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- 2. إدارة الكورسات -->
        <div id="courses" class="tab-content">
            <div class="card">
                <h3>➕ إضافة كورس جديد</h3>
                <form method="POST">
                    <label>اسم الكورس:</label>
                    <input type="text" name="title" placeholder="مثال: كورس المحادثة المتقدمة" required>
                    <label>وصف الكورس:</label>
                    <textarea name="description" rows="3" placeholder="نبذة عن الكورس..." required></textarea>
                    <label>الأيقونة:</label>
                    <select name="icon">
                        <option value="📖">📖 كتاب</option>
                        <option value="🗣️">🗣️ محادثة</option>
                        <option value="🎧">🎧 استماع</option>
                        <option value="🌟">🌟 مميز</option>
                    </select>
                    <button type="submit" name="add_course" class="btn btn-red" style="width:100%;">حفظ الكورس 🚀</button>
                </form>
            </div>
        </div>

        <!-- 3. رفع الملفات -->
        <div id="materials" class="tab-content">
            <div class="card">
                <h3>📂 إضافة مادة تعليمية (PDF، صوتيات، يوتيوب)</h3>
                <form method="POST">
                    <label>اختر الكورس:</label>
                    <select name="course_id" required>
                        <?php 
                        if (!empty($all_courses)) {
                            foreach($all_courses as $c) {
                                echo "<option value='{$c['id']}'>{$c['title']}</option>";
                            }
                        }
                        ?>
                    </select>
                    <label>عنوان الدرس أو الملف:</label>
                    <input type="text" name="title" placeholder="مثال: شرح القاعدة الأولى" required>
                    <label>نوع الملف:</label>
                    <select name="material_type">
                        <option value="youtube">📺 رابط يوتيوب (فيديو)</option>
                        <option value="pdf">📄 ملف PDF</option>
                        <option value="audio">🎧 ملف صوتي</option>
                    </select>
                    <label>رابط المادة (Link):</label>
                    <input type="url" name="material_link" placeholder="https://..." required>
                    <button type="submit" name="add_material" class="btn btn-green" style="width:100%;">رفع وحفظ المادة 📁</button>
                </form>
            </div>
        </div>

        <!-- 4. إضافة رابط امتحان التقييم -->
        <div id="exams" class="tab-content">
            <div class="card">
                <h3>📝 إضافة رابط اختبار تحديد المستوى</h3>
                <form method="POST">
                    <label>اختر الكورس المرتبط:</label>
                    <select name="course_id" required>
                        <?php 
                        if (!empty($all_courses)) {
                            foreach($all_courses as $c) {
                                echo "<option value='{$c['id']}'>{$c['title']}</option>";
                            }
                        }
                        ?>
                    </select>
                    <label>عنوان الاختبار:</label>
                    <input type="text" name="exam_title" placeholder="مثال: اختبار تحديد المستوى الشامل" required>
                    
                    <label>رابط الاختبار (Google Form أو رابط خارجي):</label>
                    <input type="url" name="exam_link" placeholder="https://forms.gle/..." required>
                    
                    <button type="submit" name="add_exam_link" class="btn btn-blue" style="width:100%;">حفظ ونشر رابط الاختبار 🎯</button>
                </form>
            </div>
        </div>

        <!-- 5. بث الزوم -->
        <div id="zoom" class="tab-content">
            <div class="card" style="border-color: var(--accent); border-width: 2px; border-style: solid;">
                <h3 style="color: var(--accent);">🔴 بث مباشر (Zoom Live)</h3>
                <form method="POST">
                    <label>اختر الكورس:</label>
                    <select name="course_id" required>
                        <?php 
                        if (!empty($all_courses)) {
                            foreach($all_courses as $c) {
                                echo "<option value='{$c['id']}'>{$c['title']}</option>";
                            }
                        }
                        ?>
                    </select>
                    <label>رابط غرفة الزوم:</label>
                    <input type="url" name="zoom_link" placeholder="https://zoom.us/j/..." required>
                    <button type="submit" name="start_zoom" class="btn btn-red" style="width:100%; margin-bottom:10px;">إطلاق البث المباشر 🔴</button>
                    <button type="submit" name="stop_zoom" class="btn" style="background:#64748b; width:100%;" formnovalidate>إيقاف البث ⏹️</button>
                </form>
            </div>
        </div>

        <!-- 6. حظر الطلاب -->
        <div id="students" class="tab-content">
            <div class="card">
                <h3>🚫 إدارة حظر الطلاب</h3>
                <form method="POST">
                    <label>إيميل الطالب:</label>
                    <input type="email" name="student_email" placeholder="student@englishwitch.com" required>
                    <div style="display: flex; gap: 10px;">
                        <button type="submit" name="toggle_ban" value="banned" class="btn btn-red" style="flex:1;">حظر ⛔</button>
                        <button type="submit" name="toggle_ban" value="active" class="btn btn-green" style="flex:1;">تنشيط ✅</button>
                        <input type="hidden" name="action_type" id="action_type">
                    </div>
                </form>
            </div>
        </div>

    </div>

    <script>
        function openTab(tabName, element) {
            let tabs = document.getElementsByClassName("tab-content");
            for (let i = 0; i < tabs.length; i++) { tabs[i].classList.remove("active"); }
            let links = document.getElementsByClassName("nav-links")[0].getElementsByTagName("li");
            for (let i = 0; i < links.length; i++) { links[i].classList.remove("active"); }
            document.getElementById(tabName).classList.add("active");
            element.classList.add("active");
            
            document.getElementById("page-title").innerText = element.innerText.replace(/[🔴📂📝🚫📚🔔]/g, '').trim();

            if (window.innerWidth <= 768) {
                toggleSidebar();
            }
        }

        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('active');
            document.querySelector('.overlay').classList.toggle('active');
        }

        document.querySelectorAll('button[name="toggle_ban"]').forEach(btn => {
            btn.addEventListener('click', function() {
                document.getElementById('action_type').value = this.value;
            });
        });
    </script>
</body>
</html>
