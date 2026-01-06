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

if ($custom_id > 0) {
    $stmt = $conn->prepare("SELECT basket_id FROM baskets WHERE customer_id = ? AND is_ordered = 0 LIMIT 1");
    $stmt->bind_param("i", $_SESSION['customer_id']);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res && $res->num_rows > 0) {
        $basket_id = $res->fetch_assoc()['basket_id'];

        $deleteStmt = $conn->prepare("DELETE FROM basket_customizations WHERE id = ? AND basket_id = ?");
        $deleteStmt->bind_param("ii", $custom_id, $basket_id);
        $deleteStmt->execute();
        $deleteStmt->close();

        $checkStmt = $conn->prepare("
            SELECT 1 FROM basket_products WHERE basket_id = ?
            UNION
            SELECT 1 FROM basket_sets WHERE basket_id = ?
            UNION
            SELECT 1 FROM basket_customizations WHERE basket_id = ?
            LIMIT 1
        ");
        $checkStmt->bind_param("iii", $basket_id, $basket_id, $basket_id);
        $checkStmt->execute();
        $checkRes = $checkStmt->get_result();

        if ($checkRes->num_rows === 0) {
            echo "<script>alert('Your cart is empty!'); window.location.href = 'main_client_page.php';</script>";
            exit;
        }
    }
    $stmt->close();
}

header("Location: client_basket.php");
exit;
