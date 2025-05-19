<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false]);
    exit;
}

$db_host = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "t3";
$db_port = 3308;

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name, $db_port);
if ($conn->connect_errno) {
    echo json_encode(['success' => false]);
    exit;
}

$user_id = $_SESSION['user_id'];
$sql = "SELECT name, avatar FROM user WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($name, $avatar);
if ($stmt->fetch()) {
    echo json_encode([
        'success' => true,
        'name' => $name,
        'avatar' => $avatar
    ]);
} else {
    echo json_encode(['success' => false]);
}
$stmt->close();
$conn->close();