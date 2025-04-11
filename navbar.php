<?php
// navbar.php

// 確認會話是否已啟動
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
?>

<nav class="navbar">
    <ul class="navbar-menu">
        <li class="navbar-item"><a href="index.php" class="navbar-link">首頁</a></li>
        <li class="navbar-item"><a href="manage.php" class="navbar-link">管理文章</a></li>
        <li class="navbar-item"><a href="post_article.php" class="navbar-link">發布文章</a></li>
        <li class="navbar-item"><a href="logout.php" class="navbar-link">登出</a></li>
    </ul>
</nav>

<style>
    body {
        font-family: Arial, sans-serif;
    }

    .navbar {
        background-color: #333;
        overflow: hidden;
    }

    .navbar-menu {
        list-style-type: none;
        margin: 0;
        padding: 0;
        display: flex;
        justify-content: flex-end;
    }

    .navbar-item {
        float: left;
    }

    .navbar-link {
        display: block;
        color: white;
        text-align: center;
        padding: 14px 20px;
        text-decoration: none;
    }

    .navbar-link:hover {
        background-color: #ddd;
        color: black;
    }
</style>