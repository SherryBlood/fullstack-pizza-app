<?php
session_start();


if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    echo '<script>alert("Доступ заборонено."); window.location.href = "main_client_page.php";</script>';
    exit;
}


$conn = new mysqli("localhost", "root", "1234", "pizza_db");
$conn->set_charset("utf8");

if ($conn->connect_error) {
    die("Connection Error: " . $conn->connect_error);
}


$ids = $_POST['client_ids'];
$first_names = $_POST['first_names'];
$last_names = $_POST['last_names'];
$emails = $_POST['emails'];
$phones = $_POST['phones'];
$addresses = $_POST['addresses'];

$success = true;

for ($i = 0; $i < count($ids); $i++) {
    $stmt = $conn->prepare("UPDATE customers SET first_name = ?, last_name = ?, email = ?, phone_number = ?, address = ? WHERE customer_id = ?");
    if ($stmt) {
        $stmt->bind_param(
            "sssssi",
            $first_names[$i],
            $last_names[$i],
            $emails[$i],
            $phones[$i],
            $addresses[$i],
            $ids[$i]
        );
        if (!$stmt->execute()) {
            $success = false;
        }
        $stmt->close();
    } else {
        $success = false;
    }
}

$conn->close();

if ($success) {
    echo '<script>alert("Changes saved successfully!"); window.location.href = "clients_admin_page.php";</script>';
} else {
    echo '<script>alert("Error saving changes."); window.history.back();</script>';
}
?>
