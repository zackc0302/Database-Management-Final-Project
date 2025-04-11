<?php
require 'config.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $article_id = $_POST['article_id'];
    $content = $_POST['content'];

    $sql = "INSERT INTO Comments (article_id, user_id, content, comm_date) VALUES (:article_id, :user_id, :content, NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':article_id', $article_id);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->bindParam(':content', $content);
    $stmt->execute();

    header('Location: index.php');
    exit;
}
