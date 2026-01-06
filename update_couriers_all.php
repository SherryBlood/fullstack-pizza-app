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


$ids = $_POST['courier_ids'];
$first_names = $_POST['first_names'];
$last_names = $_POST['last_names'];
$phones = $_POST['phones'];

$success = true;

for ($i = 0; $i < count($ids); $i++) {
    $stmt = $conn->prepare("UPDATE couriers SET first_name = ?, last_name = ?, phone_number = ? WHERE courier_id = ?");
    if ($stmt) {
        $stmt->bind_param(
            "sssi",
            $first_names[$i],
            $last_names[$i],
            $phones[$i],
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
    echo '<script>alert("Changes saved successfully!"); window.location.href = "couriers_admin_page.php";</script>';
} else {
    echo '<script>alert("Error saving changes."); window.history.back();</script>';
}
?>
