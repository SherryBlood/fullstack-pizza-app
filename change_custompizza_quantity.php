<?php
session_start();

$conn = new mysqli("localhost", "root", "1234", "pizza_db");
$conn->set_charset("utf8");

if ($conn->connect_error) {
    die("Connection Error: " . $conn->connect_error);
}

if (!isset($_SESSION['customer_id']) || $_SESSION['customer_logged_in'] !== true) {
    die("Unauthorized");
}

$custom_id = (int)($_POST['id'] ?? 0);
$quantity = (int)($_POST['quantity'] ?? 1);

if ($custom_id > 0 && $quantity > 0) {
    $stmt = $conn->prepare("UPDATE basket_customizations SET quantity = ? WHERE id = ? AND basket_id = (SELECT basket_id FROM baskets WHERE customer_id = ? AND is_ordered = 0 LIMIT 1)");
    $stmt->bind_param("iii", $quantity, $custom_id, $_SESSION['customer_id']);
    $stmt->execute();
    $stmt->close();
}

header("Location: client_basket.php");
exit;
?>
