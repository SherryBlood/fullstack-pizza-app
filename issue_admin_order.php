<?php
session_start();
date_default_timezone_set('Europe/Kyiv');

$conn = new mysqli("localhost", "root", "1234", "pizza_db");
$conn->set_charset("utf8");

if ($conn->connect_error) {
    die("Connection Error: " . $conn->connect_error);
}

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    die("Unauthorize");
}

$order_id = (int)($_POST['order_id'] ?? 0);
if ($order_id === 0) {
    die("Invalid Order ID");
}


$update = $conn->prepare("UPDATE orders SET order_status = 'issued' WHERE order_id = ?");
$update->bind_param("i", $order_id);
$update->execute();
$update->close();


$now = date("Y-m-d H:i:s");
$insert = $conn->prepare("INSERT INTO issued_orders (order_id, issued_at) VALUES (?, ?)");
$insert->bind_param("is", $order_id, $now);
$insert->execute();
$insert->close();

header("Location: getting_orders_admin_page.php");
exit;
