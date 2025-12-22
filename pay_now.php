<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Get payment method and total
$method = $_POST['method'] ?? '';
$total = $_POST['total_amount'] ?? 0;

// Handle Receipt Payment
if($method === 'receipt') {

    if(isset($_FILES['receipt_image']) && $_FILES['receipt_image']['error'] === 0) {
        $uploadDir = 'uploads/recus/';
        if(!is_dir($uploadDir)){
            mkdir($uploadDir, 0777, true);
        }

        $filename = uniqid() . '_' . basename($_FILES['receipt_image']['name']);
        $targetFile = $uploadDir . $filename;

        if(move_uploaded_file($_FILES['receipt_image']['tmp_name'], $targetFile)){
            // Insert into 'recus' table
            // Assuming paiement_id = user_id for now, can be replaced with order ID if you have orders table
            $stmt = $conn->prepare("INSERT INTO recus (paiement_id, fichier) VALUES (?, ?)");
            $stmt->bind_param("is", $user_id, $filename);
            $stmt->execute();

            $success = true;
        } else {
            $error = "Failed to upload receipt.";
        }
    } else {
        $error = "Please select a receipt image.";
    }

} else {
    // For other payment methods, simulate success
    $success = true;
}

// Optionally: clear cart
$stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();

// Redirect or show success message
if(isset($success) && $success){
    header("Location: payment_success.php");
    exit;
} else {
    echo "<p style='color:red;'>$error</p><a href='payment.php'>Go back</a>";
}
