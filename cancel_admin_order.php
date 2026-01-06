<?php
session_start();
date_default_timezone_set('Europe/Kyiv');


if (empty($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    die("Unauthorized (Admin)");
}

$conn = new mysqli("localhost", "root", "1234", "pizza_db");
$conn->set_charset("utf8");

if ($conn->connect_error) {
    die("Connection Error: " . $conn->connect_error);
}

$order_id = (int)($_POST['order_id'] ?? 0);
if ($order_id === 0) {
    die("Invalid Order ID");
}


$check = $conn->prepare("
    SELECT order_status FROM orders
    WHERE order_id = ? AND order_status NOT IN ('cancelled', 'issued')
");
$check->bind_param("i", $order_id);
$check->execute();
$res = $check->get_result();

if ($res->num_rows === 0) {
    echo "<script>alert('This order can no longer be cancelled.'); window.history.back();</script>";
    exit;
}

$update = $conn->prepare("UPDATE orders SET order_status = 'cancelled' WHERE order_id = ?");
$update->bind_param("i", $order_id);
$update->execute();

echo "<script>alert('Order cancelled.'); window.location.href = 'getting_orders_admin_page.php';</script>";
exit;
?>
