<?php
require 'config.php';
require 'premium.check.php';

// 確認會話是否已啟動
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// 確認用戶已登入
$user_id = $_SESSION['user_id'];

if (isPremium($user_id, $conn) == 1) {
    header('Location: premiumindex.php');
    exit;
} elseif (isPremium($user_id, $conn) == 2) {
    header('Location: admin_dashboard.php');
    exit;
}

// 獲取用戶資訊
$sql = "SELECT username FROM user_account WHERE user_id = :user_id";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// 獲取用戶餘額
$userBalance = getUserBalance($user_id, $conn);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>免費午餐分享平台</title>
    <link rel="stylesheet" href="styles.css">
    <link rel='stylesheet' href='https://fullcalendar.io/releases/fullcalendar/3.9.0/fullcalendar.min.css' />
    <script src='https://fullcalendar.io/releases/fullcalendar/3.9.0/lib/jquery.min.js'></script>
    <script src='https://fullcalendar.io/releases/fullcalendar/3.9.0/lib/moment.min.js'></script>
    <script src='https://fullcalendar.io/releases/fullcalendar/3.9.0/fullcalendar.min.js'></script>
</head>

<body>
    <?php require 'navbar.php'; ?>

    <div class="container">
        <header>
            <h1>歡迎，<?php echo htmlspecialchars($user['username']); ?>！</h1>
        </header>

        <h2>免費午餐活動行事曆</h2>
        <div id='calendar'></div>

        <h2>最新貼文</h2>
        <div>

            <?php
            // 獲取最新的貼文
            $sql = "SELECT A.article_id, A.title, A.content, A.art_date, A.rate, U.username, A.img
                FROM articles A
                JOIN user_account U ON A.user_id = U.user_id
                ORDER BY A.art_date DESC
                LIMIT 5";
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $articles = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($articles as $article) {
                echo "<div>";
                echo "<h3>" . htmlspecialchars($article['title']) . "</h3>";
                echo "<p>" . htmlspecialchars($article['content']) . "</p>";
                // 如果有圖片，顯示圖片
                if (!empty($article['img'])) {
                    $img_path = htmlspecialchars($article['img']);
                    // 拼接成完整的 URL
                    $full_img_url = 'http://localhost' . $img_path;
                    echo "<img src='" . $full_img_url . "' alt='Article Image' style='max-width:100%; height:auto;'>";
                }
                echo "<p>發佈者：" . htmlspecialchars($article['username']) . " 發佈日期：" . htmlspecialchars($article['art_date']) . "</p>";
                echo "<p>評分： " . htmlspecialchars($article['rate']) . "</p>";
                echo '<button class="rate-btn" data-article-id="' . htmlspecialchars($article['article_id']) . '" data-rate="1">按讚</button>';
                echo '<button class="rate-btn" data-article-id="' . htmlspecialchars($article['article_id']) . '" data-rate="-1">倒讚</button>';
                echo "</div>";

                // 顯示留言按鈕
                echo '<button class="comment-btn" data-article-id="' . htmlspecialchars($article['article_id']) . '">留言</button>';

                // 顯示已有的留言
                $sql = "SELECT C.content, C.comm_date, U.username
                    FROM comments C
                    JOIN user_account U ON C.user_id = U.user_id
                    WHERE C.article_id = :article_id
                    ORDER BY C.comm_date DESC";
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(':article_id', $article['article_id']);
                $stmt->execute();
                $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);

                echo '<div class="comments">';
                foreach ($comments as $comment) {
                    echo '<p>' . htmlspecialchars($comment['username']) . ': ' . htmlspecialchars($comment['content']) . ' (' . htmlspecialchars($comment['comm_date']) . ')</p>';
                }
                echo '</div>';
            }
            // 提示升級premium以解鎖更多文章
            echo '<div class="unlock-premium">';
            echo '<p>解鎖 Premium 會員以觀看更多文章</p>';
            echo '<a href="upgrade.php" class="upgrade-button">升級到 Premium 會員</a>';
            echo '</div>';
            ?>
        </div>

        <a href="upgrade.php">升級到 Premium 會員</a>
        <a href="recharge.php">儲值點數</a>
        <p>目前點數餘額：<?php echo $userBalance; ?> 點</p>
    </div>

    <!-- 模態框 -->
    <div id="comment-modal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <form id="comment-form" method="post" action="comments.php">
                <input type="hidden" name="article_id" id="modal-article-id">
                Content: <textarea name="content" required></textarea><br>
                <input type="submit" value="Comment">
            </form>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            $('#calendar').fullCalendar({
                events: 'load_events.php'
            });

            // 顯示模態框
            $('.comment-btn').on('click', function () {
                var articleId = $(this).data('article-id');
                $('#modal-article-id').val(articleId);
                $('#comment-modal').show();
            });

            // 關閉模態框
            $('.close').on('click', function () {
                $('#comment-modal').hide();
            });

            // 當點擊模態框外部時關閉
            $(window).on('click', function (event) {
                if (event.target.id === 'comment-modal') {
                    $('#comment-modal').hide();
                }
            });

            // 處理按讚和倒讚
            $('.rate-btn').on('click', function () {
                var articleId = $(this).data('article-id');
                var rate = $(this).data('rate');
                $.post('rate.php', { article_id: articleId, rate: rate }, function (response) {
                    location.reload();
                });
            });
        });
    </script>
</body>

</html>