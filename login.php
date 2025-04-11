<?php
require 'config.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    try {
        $sql = "SELECT user_id, password FROM user_account WHERE email = :email";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['user_id'];

            // 檢查是否為管理員
            $sql = "SELECT premium FROM premium WHERE user_id = :user_id";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':user_id', $user['user_id']);
            $stmt->execute();
            $stmt->setFetchMode(PDO::FETCH_ASSOC);
            $premium = $stmt->fetch();

            if ($premium && $premium['premium'] == 2) {
                // 登入成功後如果是管理員，跳轉到管理員控制台
                header('Location: admin_dashboard.php');
            } else {
                // 否則跳轉到主頁面
                header('Location: index.php');
            }
            exit;
        } else {
            $error = "登入失敗，請檢查您的憑證。";
        }
    } catch (PDOException $e) {
        $error = '登入失敗：' . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>登入 - 免費午餐分享平台</title>
    <link rel="stylesheet" href="styles2.css">
</head>

<body>
    <div class="container">
        <div class="form-container">
            <h2>登入</h2><br>
            <form action="login.php" method="post">
                <div class="input-group">
                    <input type="email" name="email" required>
                    <label>Email：</label>
                </div>
                <br>
                <div class="input-group">
                    <input type="password" name="password" required>
                    <label>Password：</label>
                </div>
                <button type="submit" class="btn login-btn">Login</button>
                <div class="register-section">
                    <span>Don't have an account?</span>
                    <button type="button" class="btn register-btn"
                        onclick="window.location.href='register.php'">Register ➔</button>
                </div>
            </form>
        </div>
    </div>
    <script src="scripts1.js"></script>
</body>

</html>