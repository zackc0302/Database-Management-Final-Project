<?php
require 'config.php';
require 'premium.check.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

if (!isPremium($user_id, $conn) || getUserPremiumLevel($user_id, $conn) < 2) {
    echo "你沒有權限發布活動。";
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $img = $_POST['img'];
    $act_date = $_POST['act_date'];
    $rate = $_POST['rate'];
    $place = $_POST['place'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    $url = $_POST['url'];
    $lunch = $_POST['lunch'];

    try {
        $sql = "INSERT INTO activities (user_id, title, content, img, act_date, rate, place, STARTT, ENDT, URL, LUNCH)
                VALUES (:user_id, :title, :content, :img, :act_date, :rate, :place, :start_time, :end_time, :url, :lunch)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':content', $content);
        $stmt->bindParam(':img', $img);
        $stmt->bindParam(':act_date', $act_date);
        $stmt->bindParam(':rate', $rate);
        $stmt->bindParam(':place', $place);
        $stmt->bindParam(':start_time', $start_time);
        $stmt->bindParam(':end_time', $end_time);
        $stmt->bindParam(':url', $url);
        $stmt->bindParam(':lunch', $lunch);
        $stmt->execute();

        header('Location: admin_dashboard.php');
        exit;
    } catch (PDOException $e) {
        $error = '發布活動失敗：' . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>發布活動 - 免費午餐分享平台</title>
    <link rel="stylesheet" href="styles1.css">
</head>

<body>
    <div class="container">
        <div class="form-container">
            <h2>發布活動</h2><br>
            <form action="create_activities.php" method="post">
                <div class="input-group">
                    <input type="text" name="title" required>
                    <label>Title：</label>
                </div>
                <br>
                <div class="input-group">
                    <textarea name="content" required></textarea>
                    <label>Content：</label>
                </div>
                <br>
                <div class="input-group">
                    <input type="text" name="img">
                    <label>Image URL：</label>
                </div>
                <br>
                <div class="input-group">
                    <input type="date" name="act_date" required>
                    <label>Activity Date：</label>
                </div>
                <br>
                <div class="input-group">
                    <input type="text" name="rate">
                    <label>Rate：</label>
                </div>
                <br>
                <div class="input-group">
                    <input type="text" name="place" required>
                    <label>Place：</label>
                </div>
                <br>
                <div class="input-group">
                    <input type="time" name="start_time" required>
                    <label>Start Time：</label>
                </div>
                <br>
                <div class="input-group">
                    <input type="time" name="end_time" required>
                    <label>End Time：</label>
                </div>
                <br>
                <div class="input-group">
                    <input type="url" name="url">
                    <label>URL：</label>
                </div>
                <br>
                <div class="input-group">
                    <input type="text" name="lunch">
                    <label>Lunch：</label>
                </div>
                <button type="submit" class="btn">發布活動</button>
            </form>
        </div>
    </div>
</body>

</html>