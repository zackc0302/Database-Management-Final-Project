<?php
require 'config.php';
session_start();

$user_id = $_SESSION['user_id']; // 假設使用者ID存儲在會話中

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $article_id = $_POST['article_id'];
    $rate = $_POST['rate'];

    // 檢查使用者是否已經對該文章按讚或按倒讚過
    $check_sql = "SELECT * FROM Votes WHERE user_id = :user_id AND article_id = :article_id";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $check_stmt->bindParam(':article_id', $article_id, PDO::PARAM_INT);
    $check_stmt->execute();
    $vote = $check_stmt->fetch(PDO::FETCH_ASSOC);

    if ($vote === false) {
        // 使用者尚未對該文章操作過，允許按讚或按倒讚
        $sql = "UPDATE Articles SET rate = rate + :rate WHERE article_id = :article_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':rate', $rate, PDO::PARAM_INT);
        $stmt->bindParam(':article_id', $article_id, PDO::PARAM_INT);
        $stmt->execute();

        // 在 Votes 表中記錄操作
        $vote_sql = "INSERT INTO Votes (user_id, article_id, rate) VALUES (:user_id, :article_id, :rate)";
        $vote_stmt = $conn->prepare($vote_sql);
        $vote_stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $vote_stmt->bindParam(':article_id', $article_id, PDO::PARAM_INT);
        $vote_stmt->bindParam(':rate', $rate, PDO::PARAM_INT);
        $vote_stmt->execute();

        echo "success";
    } else {
        // 使用者已經對該文章操作過，檢查是否相同
        if ($vote['rate'] == $rate) {
            // 使用者點按相同評論，取消該次評論
            $sql = "UPDATE Articles SET rate = rate - :rate WHERE article_id = :article_id";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':rate', $rate, PDO::PARAM_INT);
            $stmt->bindParam(':article_id', $article_id, PDO::PARAM_INT);
            $stmt->execute();

            // 刪除 Votes 表中的記錄
            $delete_sql = "DELETE FROM Votes WHERE user_id = :user_id AND article_id = :article_id";
            $delete_stmt = $conn->prepare($delete_sql);
            $delete_stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $delete_stmt->bindParam(':article_id', $article_id, PDO::PARAM_INT);
            $delete_stmt->execute();

            echo "success";
        } else {
            // 使用者點按不同評論，更新評論
            $sql = "UPDATE Articles SET rate = rate + :new_rate - :old_rate WHERE article_id = :article_id";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':new_rate', $rate, PDO::PARAM_INT);
            $stmt->bindParam(':old_rate', $vote['rate'], PDO::PARAM_INT);
            $stmt->bindParam(':article_id', $article_id, PDO::PARAM_INT);
            $stmt->execute();

            // 更新 Votes 表中的記錄
            $update_sql = "UPDATE Votes SET rate = :rate WHERE user_id = :user_id AND article_id = :article_id";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bindParam(':rate', $rate, PDO::PARAM_INT);
            $update_stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $update_stmt->bindParam(':article_id', $article_id, PDO::PARAM_INT);
            $update_stmt->execute();

            echo "success";
        }
    }
}

// 查詢所有文章並按rate排序
$sql = "SELECT * FROM Articles ORDER BY rate DESC, ABS(rate) DESC";
$stmt = $conn->prepare($sql);
$stmt->execute();
$articles = $stmt->fetchAll(PDO::FETCH_ASSOC);
