<?php
require 'config.php';

$sql = "SELECT title, act_date AS start FROM Activities";
$stmt = $conn->prepare($sql);
$stmt->execute();
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($events);