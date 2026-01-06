<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = (int) $_POST['id'];

    $conn = new mysqli("localhost", "root", "1234", "pizza_db");
    $conn->set_charset("utf8");

    if ($conn->connect_error) {
        die('Connection Error: ' . $conn->connect_error);
    }

    $stmt = $conn->prepare("DELETE FROM sets WHERE set_id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
    $referrer = $_SERVER['HTTP_REFERER'] ?? 'menu_admin_page.php';
    echo '<script>alert("Set removed successfully!"); window.location.href = "' . $referrer . '";</script>';
    } else {
        echo '<script>alert("Error deleting set."); window.history.back();</script>';
    }

    $stmt->close();
    $conn->close();
} else {
    echo '<script>alert("Bad request."); window.history.back();</script>';
}
?>