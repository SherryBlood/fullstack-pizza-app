<?php
session_start();


if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['id'])) {
    $product_id = (int) $_POST['id'];

    $conn = new mysqli("localhost", "root", "1234", "pizza_db");
    $conn->set_charset("utf8");

    if ($conn->connect_error) {
        die("Connection Error: " . $conn->connect_error);
    }


    $stmt = $conn->prepare("UPDATE products SET promotion_id = NULL WHERE product_id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $product_id);

        if ($stmt->execute()) {
    $referrer = $_SERVER['HTTP_REFERER'] ?? 'menu_admin_page.php';
    echo '<script>alert("Promotion removed successfully!"); window.location.href = "' . $referrer . '";</script>';
    } else {
            echo '<script>alert("Error deleting promotion: ' . $stmt->error . '"); window.history.back();</script>';
        }

        $stmt->close();
    } else {
        echo '<script>alert("Bad request."); window.history.back();</script>';
    }

    $conn->close();
} else {
    echo '<script>alert("Bad request."); window.history.back();</script>';
}
?>
