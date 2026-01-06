<?php
$conn = new mysqli("localhost", "root", "1234", "pizza_db");
$conn->set_charset("utf8");

if ($conn->connect_error) {
    die("Connection Error: " . $conn->connect_error);
}

$client_id = (int) $_POST['client-id'];
$email = trim($_POST['client-email-admin']);
$new_password = $_POST['client-new-password-admin'];
$confirm_password = $_POST['client-accept-password-admin'];

if ($new_password !== $confirm_password) {
    echo '<script>alert("Passwords do not match!"); window.history.back();</script>';
    exit;
}

$password_hash = password_hash($new_password, PASSWORD_DEFAULT);

$sql = "UPDATE customers SET password_hash = ? WHERE customer_id = ? AND email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sis", $password_hash, $client_id, $email);

if ($stmt->execute()) {
    echo '<script>alert("Password changed successfully!"); window.location.href="clients_admin_page.php";</script>';
} else {
    echo '<script>alert("Error changing password: ' . $stmt->error . '"); window.history.back();</script>';
}

$stmt->close();
$conn->close();
?>
