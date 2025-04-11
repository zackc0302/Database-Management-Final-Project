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
    echo "你沒有權限訪問此頁面。";
    exit;
}

// 獲取用戶發佈的活動
$sql = "SELECT * FROM activities WHERE user_id = :user_id";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$activities = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 將活動轉換為行事曆事件格式
$events = [];
foreach ($activities as $activity) {
    $events[] = [
        'title' => htmlspecialchars($activity['title']),
        'start' => htmlspecialchars($activity['act_date'] . 'T' . $activity['STARTT']),
        'end' => htmlspecialchars($activity['act_date'] . 'T' . $activity['ENDT']),
        'description' => htmlspecialchars($activity['content'])
    ];
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>管理員控制台 - 免費午餐分享平台</title>
    <link rel="stylesheet" href="styles1.css">
    <link rel='stylesheet' href='https://fullcalendar.io/releases/fullcalendar/3.9.0/fullcalendar.min.css' />
    <script src='https://fullcalendar.io/releases/fullcalendar/3.9.0/lib/jquery.min.js'></script>
    <script src='https://fullcalendar.io/releases/fullcalendar/3.9.0/lib/moment.min.js'></script>
    <script src='https://fullcalendar.io/releases/fullcalendar/3.9.0/fullcalendar.min.js'></script>
    <style>
        #calendar {
            max-width: 900px;
            margin: 0 auto;
            padding: 20px;
        }
    </style>
</head>

<body>
    <div class="container">
        <header>
            <h1>管理員控制台</h1>
        </header>

        <h2>你的活動</h2>
        <a href="create_activities.php" class="btn">發布新活動</a>
        <a href="login.php" class="btn">登出</a>

        <div id='calendar'></div>

        <div id='activities'>
            <?php foreach ($activities as $activity): ?>
                <div>
                    <h3><?php echo htmlspecialchars($activity['title']); ?></h3>
                    <p><?php echo htmlspecialchars($activity['content']); ?></p>
                    <?php if (!empty($activity['img'])): ?>
                        <img src="<?php echo htmlspecialchars($activity['img']); ?>" alt="Activity Image"
                            style="max-width:100%; height:auto;">
                    <?php endif; ?>
                    <p>活動日期：<?php echo htmlspecialchars($activity['act_date']); ?></p>
                    <p>評分：<?php echo htmlspecialchars($activity['rate']); ?></p>
                    <p>地點：<?php echo htmlspecialchars($activity['place']); ?></p>
                    <p>開始時間：<?php echo htmlspecialchars($activity['STARTT']); ?></p>
                    <p>結束時間：<?php echo htmlspecialchars($activity['ENDT']); ?></p>
                    <p>網址：<a
                            href="<?php echo htmlspecialchars($activity['URL']); ?>"><?php echo htmlspecialchars($activity['URL']); ?></a>
                    </p>
                    <p>午餐：<?php echo htmlspecialchars($activity['LUNCH']); ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <script>
        $(document).ready(function () {
            $('#calendar').fullCalendar({
                events: <?php echo json_encode($events); ?>,
                eventRender: function (event, element) {
                    element.qtip({
                        content: event.description
                    });
                }
            });
        });
    </script>
</body>

</html>