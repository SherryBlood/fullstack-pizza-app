<?php


session_start();


$conn = new mysqli("localhost", "root", "1234", "pizza_db");
$conn->set_charset("utf8");

if ($conn->connect_error) {
    die("Connection Error: " . $conn->connect_error);
}


$client_id = (int) $_SESSION['customer_id'];
$current_password = $_POST['client-now-pass-changge'] ?? '';
$new_password = $_POST['client-new-password-changge'] ?? '';
$confirm_password = $_POST['client-accept-password-changge'] ?? '';

if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
    echo '<script>alert("Please fill in all fields."); window.history.back();</script>';
    exit;
}

if ($new_password !== $confirm_password) {
    echo '<script>alert("New password and confirmation do not match."); window.history.back();</script>';
    exit;
}


$stmt = $conn->prepare("SELECT password_hash FROM customers WHERE customer_id = ?");
$stmt->bind_param("i", $client_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    $user = $result->fetch_assoc();

    if (!password_verify($current_password, $user['password_hash'])) {
        echo '<script>alert("Incorrect current password."); window.history.back();</script>';
        exit;
    }

    $new_hash = password_hash($new_password, PASSWORD_DEFAULT);

    $update = $conn->prepare("UPDATE customers SET password_hash = ? WHERE customer_id = ?");
    $update->bind_param("si", $new_hash, $client_id);

    if ($update->execute()) {
        echo '<script>alert("Password changed successfully."); window.location.href="client_page.php";</script>';
    } else {
        echo '<script>alert("Error updating password."); window.history.back();</script>';
    }

    $update->close();
} else {
    echo '<script>alert("User not found."); window.history.back();</script>';
}

$stmt->close();
$conn->close();
?>
