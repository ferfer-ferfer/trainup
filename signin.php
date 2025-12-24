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

        // Check if profile is complete
        // Adjust column names if necessary: profile_picture, gender, address, bio
        $profileComplete = !empty($user['profile_picture']) && !empty($user['gender']) && !empty($user['address']) && !empty($user['bio']);

        if ($profileComplete) {
            // Profile complete, redirect to dashboard
            echo "
            <script>
                localStorage.setItem('user_id', '{$user['id']}');
                window.location.href = 'dashboard.php';
            </script>
            ";
        } else {
            // Profile incomplete, redirect to information.php
            echo "
            <script>
                localStorage.setItem('user_id', '{$user['id']}');
                window.location.href = 'information.php';
            </script>
            ";
        }
        exit();
    } else {
        echo "<script>alert('Wrong password!'); window.history.back();</script>";
    }
} else {
    echo "<script>alert('User not found!'); window.history.back();</script>";
}
?>

