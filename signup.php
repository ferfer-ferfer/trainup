<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';
require 'db.php';

if($_SERVER["REQUEST_METHOD"] === "POST"){

    $email = trim($_POST["email"]);

    // Check if user exists
    $stmt = $conn->prepare("SELECT id, firstname FROM users WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows === 0){
        die("Email not found.");
    }

    $user = $result->fetch_assoc();
    $user_id = $user['id'];
    $firstname = $user['firstname'];

    // Remove old codes for this user
    $stmt = $conn->prepare("DELETE FROM password_resets WHERE user_id=? AND used=0");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();

    // Generate new 6-digit code
    $reset_code = rand(100000, 999999);
    $expires_at = date("Y-m-d H:i:s", strtotime("+15 minutes"));

    // Insert code into database
    $stmt = $conn->prepare("INSERT INTO password_resets (user_id, code, expires_at) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $user_id, $reset_code, $expires_at);
    $stmt->execute();

    // Send email
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'trainupcommunity@gmail.com';
        $mail->Password   = 'YOUR_APP_PASSWORD';
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

        $mail->setFrom('trainupcommunity@gmail.com', 'TrainUp Community');
        $mail->addAddress($email, $firstname);

        $mail->isHTML(true);
        $mail->Subject = 'Reset Your Password';
        $mail->Body    = "
            Hi $firstname,<br><br>
            Your password reset code is:<br>
            <b>$reset_code</b><br><br>
            Enter this code on the website to reset your password.<br>
            It expires in 15 minutes.<br>
            If you did not request a reset, ignore this email.
        ";

        $mail->send();
        echo "Password reset code sent. Check your email.";
        header("Refresh: 3; URL=reset_password.php"); // redirect
        exit();

    } catch (Exception $e) {
        echo "Email could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}
?>
