<?php
$score = 0;
$result_message = "";
$level = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // الإجابات الصحيحة
    $answers = [
        "q1" => "am",      // I am a student.
        "q2" => "went",    // She went to the market yesterday.
        "q3" => "for",     // I have been living here for 5 years.
        "q4" => "bigger",  // An elephant is bigger than a dog.
        "q5" => "doesn't"  // He doesn't like playing football.
    ];

    // حساب الدرجة
    foreach ($answers as $question => $correct_answer) {
        if (isset($_POST[$question]) && $_POST[$question] == $correct_answer) {
            $score++;
        }
    }

    // تحديد المستوى
    if ($score <= 2) {
        $level = "مبتدئ (Beginner)";
        $result_message = "بداية موفقة! كورس المستوى الأول هو الأنسب ليك عشان تبني أساس قوي.";
    } elseif ($score <= 4) {
        $level = "متوسط (Intermediate)";
        $result_message = "مستواك ممتاز! كورس المحادثة والقواعد المتقدمة حيخليك طلقة.";
    } else {
        $level = "متقدم (Advanced)";
        $result_message = "يا سلام عليك، مستواك رهيب! كورس التوفل أو الكورسات الاحترافية هي خيارك الصح.";
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تحديد المستوى المجاني - English Witch</title>
    <style>
        :root { --primary: #2c4c65; --accent: #e51b23; --bg: #f4f6f9; --white: #ffffff; }
        body { font-family: 'Segoe UI', Tahoma, sans-serif; margin: 0; padding: 0; background: var(--bg); color: #333; }
        
        .header { background: var(--primary); color: var(--white); text-align: center; padding: 30px 20px; border-bottom: 5px solid var(--accent); }
        .header h1 { margin: 0; font-size: 28px; }
        .header p { margin-top: 10px; color: #cbd5e1; }
        
        .container { max-width: 800px; margin: 40px auto; padding: 0 20px; }
        .card { background: var(--white); padding: 30px; border-radius: 12px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); border: 1px solid #e2e8f0; margin-bottom: 20px; }
        
        .question { margin-bottom: 25px; border-bottom: 1px dashed #e2e8f0; padding-bottom: 15px; }
        .question h3 { color: var(--primary); font-size: 18px; margin-bottom: 15px; direction: ltr; text-align: left; }
        
        .options label { display: block; background: var(--bg); padding: 12px; margin-bottom: 10px; border-radius: 8px; cursor: pointer; transition: 0.3s; border: 1px solid #cbd5e1; direction: ltr; text-align: left; font-weight: bold; }
        .options label:hover { background: #e0e7ff; border-color: var(--primary); }
        .options input[type="radio"] { margin-right: 10px; }
        
        .btn-submit { background: var(--accent); color: var(--white); padding: 15px 30px; border: none; border-radius: 8px; font-size: 18px; font-weight: bold; cursor: pointer; width: 100%; transition: 0.3s; }
        .btn-submit:hover { background: #c0151d; }
        
        .result-box { text-align: center; padding: 40px; background: var(--white); border-radius: 12px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); border-top: 5px solid #10b981; }
        .result-box h2 { color: var(--primary); font-size: 32px; margin-bottom: 10px; }
        .result-box h3 { color: #10b981; margin-bottom: 20px; font-size: 24px; }
        .result-box p { font-size: 18px; color: #64748b; line-height: 1.6; margin-bottom: 30px; }
        .btn-subscribe { background: var(--primary); color: var(--white); padding: 15px 30px; text-decoration: none; border-radius: 8px; font-size: 18px; font-weight: bold; display: inline-block; transition: 0.3s; }
        .btn-subscribe:hover { background: #1e3649; }
    </style>
</head>
<body>

    <div class="header">
        <h1>🎯 اختبار تحديد المستوى</h1>
        <p>جاوب على الأسئلة دي عشان نعرف مستواك ونرشح ليك الكورس المناسب</p>
    </div>

    <div class="container">
        <?php if ($_SERVER["REQUEST_METHOD"] == "POST"): ?>
            <!-- عرض النتيجة بعد إرسال الفورم -->
            <div class="result-box">
                <h2>نتيجتك: <?php echo $score; ?> / 5</h2>
                <h3>مستواك: <?php echo $level; ?></h3>
                <p><?php echo $result_message; ?></p>
                <!-- الزرار ده بيودي لصفحة الدفع والاشتراك -->
                <a href="subscribe.php" class="btn-subscribe">اشترك في الكورس الآن 💳</a>
            </div>
        <?php else: ?>
            <!-- عرض الأسئلة لو لسه ما امتحن -->
            <div class="card">
                <form method="POST">
                    <div class="question">
                        <h3>1. I _____ a student.</h3>
                        <div class="options">
                            <label><input type="radio" name="q1" value="is" required> is</label>
                            <label><input type="radio" name="q1" value="am"> am</label>
                            <label><input type="radio" name="q1" value="are"> are</label>
                        </div>
                    </div>

                    <div class="question">
                        <h3>2. She _____ to the market yesterday.</h3>
                        <div class="options">
                            <label><input type="radio" name="q2" value="go" required> go</label>
                            <label><input type="radio" name="q2" value="goes"> goes</label>
                            <label><input type="radio" name="q2" value="went"> went</label>
                        </div>
                    </div>

                    <div class="question">
                        <h3>3. I have been living here _____ 5 years.</h3>
                        <div class="options">
                            <label><input type="radio" name="q3" value="since" required> since</label>
                            <label><input type="radio" name="q3" value="for"> for</label>
                            <label><input type="radio" name="q3" value="in"> in</label>
                        </div>
                    </div>

                    <div class="question">
                        <h3>4. An elephant is _____ than a dog.</h3>
                        <div class="options">
                            <label><input type="radio" name="q4" value="big" required> big</label>
                            <label><input type="radio" name="q4" value="bigger"> bigger</label>
                            <label><input type="radio" name="q4" value="biggest"> biggest</label>
                        </div>
                    </div>

                    <div class="question">
                        <h3>5. He _____ like playing football.</h3>
                        <div class="options">
                            <label><input type="radio" name="q5" value="don't" required> don't</label>
                            <label><input type="radio" name="q5" value="doesn't"> doesn't</label>
                            <label><input type="radio" name="q5" value="isn't"> isn't</label>
                        </div>
                    </div>

                    <button type="submit" class="btn-submit">إرسال الإجابات وعرض النتيجة ✅</button>
                </form>
            </div>
        <?php endif; ?>
    </div>

</body>
</html>
