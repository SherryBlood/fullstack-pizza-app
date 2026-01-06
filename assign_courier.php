<?php
session_start();
date_default_timezone_set('Europe/Kyiv');


if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    die("Access denied");
}

$conn = new mysqli("localhost", "root", "1234", "pizza_db");
$conn->set_charset("utf8");

if ($conn->connect_error) {
    die("Connection Error: " . $conn->connect_error);
}

$order_id = isset($_POST['order_id']) ? (int)$_POST['order_id'] : 0;
$courier_id = isset($_POST['courier_id']) ? (int)$_POST['courier_id'] : 0;

if ($order_id <= 0 || $courier_id <= 0) {
    die("Invalid data");
}


$stmt = $conn->prepare("SELECT order_datetime FROM orders WHERE order_id = ?");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Order not found");
}

$order = $result->fetch_assoc();
$order_datetime = $order['order_datetime'];
$delivery_datetime = date('Y-m-d H:i:s', strtotime($order_datetime . ' +10 minutes'));


$checkStmt = $conn->prepare("SELECT * FROM delivery WHERE order_id = ?");
$checkStmt->bind_param("i", $order_id);
$checkStmt->execute();
$checkRes = $checkStmt->get_result();

if ($checkRes->num_rows > 0) {

    $updateStmt = $conn->prepare("
        UPDATE delivery SET courier_id = ?, delivery_datetime = ? WHERE order_id = ?
    ");
    $updateStmt->bind_param("isi", $courier_id, $delivery_datetime, $order_id);
    $updateStmt->execute();
    $updateStmt->close();
} else {

    $insertStmt = $conn->prepare("
        INSERT INTO delivery (delivery_datetime, order_id, courier_id)
        VALUES (?, ?, ?)
    ");
    $insertStmt->bind_param("sii", $delivery_datetime, $order_id, $courier_id);
    $insertStmt->execute();
    $insertStmt->close();
}

$checkStmt->close();
$stmt->close();
$conn->close();

echo "<script>alert('Courier assigned'); window.history.back();</script>";
?>
