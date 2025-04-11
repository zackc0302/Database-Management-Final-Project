<?php
require 'config.php';
require 'premium.check.php';
session_start();

// 確認用戶已登入
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $amount = $_POST['amount'];
    if ($amount > 0) {
        try {
            $sql = "INSERT INTO premium (user_id, money) VALUES (:user_id, :money)
                    ON DUPLICATE KEY UPDATE money = money + :money";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->bindParam(':money', $amount);
            $stmt->execute();

            echo "儲值成功！";
            header('Location: index.php');
            exit;
        } catch (PDOException $e) {
            echo '儲值失敗：' . $e->getMessage();
        }
    } else {
        echo '儲值金額必須大於 0。';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>儲值點數</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>儲值點數</h1>
            <a href="index.php">返回首頁</a>
        </header>

        <form method="post" action="recharge.php">
            <label for="amount">儲值金額：</label>
            <input type="number" name="amount" id="amount" required>
            <button type="submit">儲值</button>
        </form>
    </div>
</body>
</html>
