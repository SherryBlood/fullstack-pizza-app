<?php 
session_start();


$conn = new mysqli("localhost", "root", "1234", "pizza_db");
$conn->set_charset("utf8");

if ($conn->connect_error) {
    die("Connection Error: " . $conn->connect_error);
}


$courier_id = (int)($_POST['courier-id'] ?? 0);
$tel = trim($_POST['courier-tel'] ?? '');

if ($courier_id === 0 || empty($tel)) {
    echo '<script>
        alert("Please fill in all fields.");
        window.history.back();
    </script>';
    exit;
}


$stmt = $conn->prepare("SELECT * FROM couriers WHERE courier_id = ?");
$stmt->bind_param("i", $courier_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    $courier = $result->fetch_assoc();


    if ($courier['phone_number'] === $tel) {
        $_SESSION['courier_logged_in'] = true;
        $_SESSION['courier_id'] = $courier['courier_id'];
        $_SESSION['courier_name'] = $courier['first_name'] . ' ' . $courier['last_name'];

        echo '<script>
            alert("Logged in.");
            window.location.href = "orders_courier_page.php";
        </script>';
        exit;
    }
}

echo '<script>
    alert("Invalid data!");
    window.history.back();
</script>';
exit;

$conn->close();
?>
