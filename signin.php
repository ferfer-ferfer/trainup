<?php
session_start();
require 'db.php';

// Get POST data safely
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

$sql = "SELECT * FROM users WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();

    if (password_verify($password, $user['password'])) {
        // Store in PHP session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];

        // Redirect to dashboard, but first store user_id in localStorage
        echo "
        <script>
            localStorage.setItem('user_id', '{$user['id']}');
            window.location.href = 'dashboard.php';
        </script>
        ";
        exit();
    } else {
        echo "<script>alert('Wrong password!'); window.history.back();</script>";
    }
} else {
    echo "<script>alert('User not found!'); window.history.back();</script>";
}
?>

