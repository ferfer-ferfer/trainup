<?php
session_start();
require 'db.php';

if (!isset($_SESSION['reset_email'])) {
    header("Location: forgot_password.php");
    exit();
}

$email = $_SESSION['reset_email'];
$error = $success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $code = trim($_POST['code']);
    $newPassword = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];

    if ($newPassword !== $confirmPassword) {
        $error = "Passwords do not match.";
    } else {
        // Verify code and expiration
        $stmt = $conn->prepare("SELECT reset_code, reset_expire FROM users WHERE email=?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if (!$user || $user['reset_code'] !== $code) {
            $error = "Invalid code.";
        } elseif (strtotime($user['reset_expire']) < time()) {
            $error = "Code expired. Please request a new one.";
        } else {
            // Update password and clear reset code
            $hashed = password_hash($newPassword, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE users SET password=?, reset_code=NULL, reset_expire=NULL WHERE email=?");
            $stmt->bind_param("ss", $hashed, $email);
            if ($stmt->execute()) {
                $success = "Password reset successfully! You can now login.";
                unset($_SESSION['reset_email']);
                header("Refresh:2; URL=login.php");
            } else {
                $error = "Database error: " . $stmt->error;
            }
        }
    }
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>TrainUp | Reset Password</title>
  <link rel="icon" href="img/Group 100.png" type="image/png">
    <link rel="stylesheet" href="css/style.css">
    <style>
        body {
    margin: 0;
    padding: 0;
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
}

.form-box {
    background: rgba(255, 255, 255, 0.08);
    border: 1px solid rgba(255, 255, 255, 0.25);
    border-radius: 12px;
    box-shadow: 0 0 25px rgba(0, 217, 255, 0.2);
    backdrop-filter: blur(10px);
    padding: 40px 50px;
    width: 350px;
    max-width: 90%;
    text-align: center;
}

.form-box h2 {
    margin-bottom: 25px;
    font-size: 2rem;
    color: #e0f7fa;
}

.form-box label {
    display: block;
    font-size: 14px;
    color: #cce7ff;
    margin: 10px 0 6px;
    text-align: left;
}

.form-box input[type="text"],
.form-box input[type="email"],
.form-box input[type="password"] {
    width: 100%;
    padding: 10px 12px;
    border-radius: 8px;
    border: 1px solid #00bfff;
    background: transparent;
    color: #fff;
    font-size: 14px;
    outline: none;
    transition: 0.3s;
}

.form-box input:focus {
    border-color: #00e5ff;
    box-shadow: 0 0 6px #00e5ff;
}

.signin-btn {
    width: 100%;
    padding: 12px;
    margin-top: 20px;
    background: #00d5ff;
    color: #001f54;
    font-weight: 600;
    border: none;
    border-radius: 20px;
    cursor: pointer;
    font-size: 16px;
    transition: 0.3s;
}

.signin-btn:hover {
    background: #00a9cc;
    transform: scale(1.03);
}

.switch-link {
    margin-top: 15px;
}

.switch-link a {
    color: #00d5ff;
    text-decoration: none;
    font-weight: 600;
    transition: 0.3s;
}

.switch-link a:hover {
    text-decoration: underline;
}

.message {
    margin-bottom: 15px;
    font-weight: 600;
}

.message.error { color: #ff6b6b; }
.message.success { color: #7cff6b; }

/* Responsive */
@media (max-width:480px){
    .form-box{
        padding: 25px 20px;
    }
    .form-box h2{
        font-size: 1.6rem;
    }
}
    </style>
</head>
<body>
    <div class="form-box">
  <h2>Reset Password</h2>
  <form action="reset_password.php" method="POST">
    <label>Email Address</label>
    <input type="email" name="email" placeholder="Enter your email" required>

    <label>Verification Code</label>
    <input type="text" name="code" placeholder="Enter 6-digit code" required>

    <label>New Password</label>
    <input type="password" name="password" placeholder="New Password" required>

    <label>Confirm Password</label>
    <input type="password" name="confirm_password" placeholder="Confirm Password" required>

    <button type="submit" class="signin-btn">Reset Password</button>

    <div class="switch-link">
      Remembered your password? <a href="login.php">Sign In</a>
    </div>
  </form>
</div>

</body>
</html>