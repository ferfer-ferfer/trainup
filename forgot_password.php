<?php
session_start();
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // PHPMailer
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);

    // Check if email exists
    $stmt = $conn->prepare("SELECT firstname FROM users WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 0) {
        die("Email not found.");
    }
    $user = $result->fetch_assoc();

    // Generate 6-digit code and expiration (10 minutes)
    $code = rand(100000, 999999);
    $expires = date("Y-m-d H:i:s", strtotime("+10 minutes"));

    // Store code + expiration in database
    $stmt = $conn->prepare("UPDATE users SET reset_code=?, reset_expire=? WHERE email=?");
    $stmt->bind_param("sss", $code, $expires, $email);
    $stmt->execute();

    // Store email in session for reset page
    $_SESSION['reset_email'] = $email;

    // Send email with PHPMailer
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'trainupcommunity@gmail.com';
        $mail->Password   = 'kdvk tcjf phzp dpis'; // your app password
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

        $mail->setFrom('trainupcommunity@gmail.com', 'TrainUp');
        $mail->addAddress($email, $user['firstname']);

        $mail->isHTML(true);
        $mail->Subject = 'Password Reset Code';
        $mail->Body    = "Hi {$user['firstname']},<br>Your password reset code is: <b>$code</b><br>It expires in 10 minutes.";

        $mail->send();
        header("Location: reset_password.php");
        exit();

    } catch (Exception $e) {
        echo "Failed to send email: {$mail->ErrorInfo}";
    }
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TrainUp | Forgot Password</title>
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

.form-box input[type="email"] {
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

.form-box input[type="email"]:focus {
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
  <h2>Forgot Password</h2>
  <form action="forgot_password.php" method="POST">
    <label>Email Address</label>
    <input type="email" name="email" placeholder="Enter your email" required>

    <button type="submit" class="signin-btn">Send Reset Code</button>

    <div class="switch-link">
      Remembered your password? <a href="login.php">Sign In</a>
    </div>
  </form>
</div>

</body>
</html>