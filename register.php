<?php
require 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $birth_date = $_POST['birth_date'];
    $is_admin = isset($_POST['is_admin']) ? 1 : 0;

    if ($_POST['password'] !== $_POST['confirm_password']) {
        $error = "密碼不匹配，請重新輸入。";
    } else {
        try {
            $sql = "INSERT INTO user_account (email, username, birth_date, password, reg_date)
                    VALUES (:email, :username, :birth_date, :password, NOW())";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':birth_date', $birth_date);
            $stmt->bindParam(':password', $password);
            $stmt->execute();

            $user_id = $conn->lastInsertId();

            // 如果是管理員，設置 premium 等級為 2
            if ($is_admin) {
                $sql = "INSERT INTO premium (user_id, premium, money) VALUES (:user_id, 2, 0)";
            } else {
                $sql = "INSERT INTO premium (user_id, premium, money) VALUES (:user_id, 0, 0)";
            }
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->execute();

            // 註冊成功後跳轉到登入頁面
            header('Location: login.php');
            exit;
        } catch (PDOException $e) {
            $error = '註冊失敗：' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>註冊 - 免費午餐分享平台</title>
    <link rel="stylesheet" href="styles1.css">
</head>

<body>
    <div class="container">
        <div class="form-container">
            <h2>註冊</h2><br>
            <form action="register.php" method="post">
                <div class="input-group">
                    <input type="email" name="email" required>
                    <label>Email：</label>
                </div>
                <br>
                <div class="input-group">
                    <input type="text" name="username" required>
                    <label>Username：</label>
                </div>
                <br>
                <div class="input-group">
                    <input type="password" name="password" required>
                    <label>Password：</label>
                </div>
                <br>
                <div class="input-group">
                    <input type="password" name="confirm_password" required>
                    <label>Confirm Password：</label>
                </div>
                <br>
                <div class="input-group">
                    <input type="date" name="birth_date" required>
                    <label>Birth Date：</label>
                </div>
                <div class="input-group">
                    <input type="checkbox" name="is_admin" id="is_admin">
                    <label for="is_admin">管理員註冊</label>
                </div>
                <button type="submit" class="btn register-btn">Register</button>
                <div class="login-section">
                    <span>Already have an account?</span>
                    <button type="button" class="btn login-btn" onclick="window.location.href='login.php'">Login
                        ➔</button>
                </div>
            </form>
        </div>
    </div>
    <script src="scripts1.js"></script>
</body>

</html>