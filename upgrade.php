//upgrade.php
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

// 獲取用戶餘額
$sql = "SELECT money FROM premium WHERE user_id = :user_id";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$result = $stmt->fetch(PDO::FETCH_ASSOC);

if ($result) {
    $userBalance = $result['money'];
} else {
    $userBalance = 0; // 或者您希望的默認值
}

$upgradeCost = 30;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 檢查用戶是否有足夠餘額
    if ($userBalance >= $upgradeCost) {
        // 扣除餘額並升級會員
        try {
            $conn->beginTransaction();

            $newBalance = $userBalance - $upgradeCost;

            // 更新用戶餘額並將用戶升級為 Premium
            $sql = "UPDATE premium SET money = :new_balance, premium = 1 WHERE user_id = :user_id";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':new_balance', $newBalance);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->execute();

            $conn->commit();

            echo "<script>alert('升級成功！'); window.location.href='premiumindex.php';</script>";
            exit;
        } catch (PDOException $e) {
            $conn->rollBack();
            echo '升級失敗：' . $e->getMessage();
        }
    } else {
        echo "<script>alert('您的餘額不足以升級，請先儲值。'); window.location.href='recharge.php';</script>";
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>升級到 Premium 會員</title>
    <style>
        body {
    font-family: Arial, sans-serif;
    background-color: #f4f4f4;
    margin: 0;
    padding: 0;
    }

    .container {
        max-width: 800px;
        margin: 50px auto;
        padding: 20px;
        background-color: #fff;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        border-radius: 8px;
    }

    header {
        text-align: center;
        margin-bottom: 20px;
    }

    header h1 {
        margin: 0;
        color: #333;
    }

    header a {
        color: #007bff;
        text-decoration: none;
    }

    .balance {
        text-align: center;
        margin-bottom: 20px;
    }

    .plans {
        display: flex;
        justify-content: center;
    }

    .plan {
        background-color: #e3e3e3;
        border-radius: 8px;
        padding: 20px;
        width: 300px;
        text-align: center;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    .plan h2 {
        color: #333;
    }

    .plan p {
        font-size: 18px;
        color: #666;
    }

    .plan ul {
        list-style: none;
        padding: 0;
        text-align: left;
    }

    .plan ul li {
        background: url('checkmark.png') no-repeat left center;
        padding-left: 20px;
        margin-bottom: 10px;
    }

    .plan button {
        background-color: #007bff;
        color: #fff;
        border: none;
        padding: 10px 20px;
        border-radius: 5px;
        cursor: pointer;
        font-size: 16px;
    }

    .plan button:hover {
        background-color: #0056b3;
    }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>升級到 Premium 會員</h1>
            <a href="index.php">返回首頁</a>
        </header>
        <div class="balance">
            <p>您的餘額：<?php echo $userBalance; ?> 元</p>
        </div>
        <form method="post" action="upgrade.php">
            <div class="plans">
                <div class="plan">
                    <h2>乞丐超人</h2>
                    <p>30 元/月</p>
                    <ul>
                        <li>可在留言使用的自訂表情符號</li>
                        <li>更改帳號名字的顏色</li>
                        <li>優先置頂乞丐超人的留言</li>
                        <li>更精緻的UI</li>
                    </ul>
                    <button type="submit">購買基本方案</button>
                </div>
            </div>
        </form>
    </div>
</body>
</html>
