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

// Flag to track success
$success = false;
$error = "";

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

if($success){
    // Fetch all cart items for enrollment
    $stmt = $conn->prepare("SELECT course_id FROM cart WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $course_ids = [];
    while($row = $result->fetch_assoc()){
        $course_ids[] = $row['course_id'];
    }

    // Enroll user in courses if not already enrolled
    foreach($course_ids as $course_id){
        $check = $conn->prepare("SELECT id FROM enrollments WHERE user_id = ? AND course_id = ?");
        $check->bind_param("ii", $user_id, $course_id);
        $check->execute();
        $check->store_result();

        if($check->num_rows === 0){
            $insert = $conn->prepare("INSERT INTO enrollments (user_id, course_id, progress, enrolled_at) VALUES (?, ?, 0, NOW())");
            $insert->bind_param("ii", $user_id, $course_id);
            $insert->execute();
        }
        $check->close();
    }

    // Clear the cart
    $stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();

    // Redirect to success page
    header("Location: payment_success.php");
    exit;
} else {
    echo "<p style='color:red;'>$error</p><a href='payment.php'>Go back</a>";
}
?>
