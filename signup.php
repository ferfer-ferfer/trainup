<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

require 'db.php';

if($_SERVER["REQUEST_METHOD"] === "POST"){

    $firstname = trim($_POST["firstname"]);
    $lastname  = trim($_POST["lastname"]);
    $username  = trim($_POST["username"]);
    $phone     = trim($_POST["phoneNumber"]);
    $email     = trim($_POST["email"]);
    $password  = $_POST["password"];
    $confirm   = $_POST["confirm_password"];

    if($password !== $confirm){
        die("Passwords don't match");
    }

    // ========== CHECK DUPLICATES ========== //

    // check username only in verified users :
    $stmt = $conn->prepare("SELECT id FROM users WHERE username=?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    if($stmt->get_result()->num_rows > 0){
        die("Username already taken.");
    }

    // check email in verified users
    $stmt = $conn->prepare("SELECT id FROM users WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    if($stmt->get_result()->num_rows > 0){
        die("Email already verified. Please login.");
    }

    // if email exists in pending, remove old pending info
    $stmt = $conn->prepare("DELETE FROM pending_users WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();

    // ====================================== //

    $hashed=password_hash($password,PASSWORD_DEFAULT);
    $verification_code = rand(100000, 999999); // 6-digit code
    $sql="INSERT INTO pending_users 
        (firstname, lastname, username, email, password, phone, verification_token)
        VALUES (?,?,?,?,?,?,?)";

    $stmt=$conn->prepare($sql);
    $stmt->bind_param("sssssss",
        $firstname,
        $lastname,
        $username,
        $email,
        $hashed,
        $phone,
        $verification_code
    );

    if($stmt->execute()){


 


$mail = new PHPMailer(true);
try {
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'trainupcommunity@gmail.com';
    $mail->Password   = 'kdvk tcjf phzp dpis';
    $mail->SMTPSecure = 'tls';
    $mail->Port       = 587;

    $mail->setFrom('trainupcommunity@gmail.com', 'TrainUp Community');
    $mail->addAddress($email, $firstname);

    $mail->isHTML(true);
    $mail->Subject = 'Verify Your Email';
    $mail->Body    = "
        Hi $firstname,<br><br>
        Your verification code is:<br>
        <b>$verification_code</b><br><br>
        Enter this code on the website to verify your account.<br>
        If you did not register, ignore this email.
    ";

    $mail->send();
echo "Registration pending. Check your email for the verification code.";
header("Refresh: 3; URL=verify.php"); // redirect after 3 seconds
exit();

} catch (Exception $e) {
    echo "Email could not be sent. Mailer Error: {$mail->ErrorInfo}";
}


    } else {
        echo "Database error: ".$stmt->error;
    }

}
?>


