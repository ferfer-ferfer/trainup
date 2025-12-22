<?php
session_start();
include "db.php"; // connexion à la BDD

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if (isset($_POST['course_id'])) {
    $user_id = $_SESSION['user_id'];
    $course_id = (int)$_POST['course_id'];

    // Check course exists
    $stmt = $conn->prepare("SELECT * FROM course WHERE id = ?");
    $stmt->bind_param("i", $course_id);
    $stmt->execute();
    $course = $stmt->get_result()->fetch_assoc();

    if (!$course) {
        die("Course not found");
    }

    $price = $course['price'];
    $quantity = 1;

    // Check if course already in cart
    $stmt = $conn->prepare("SELECT * FROM cart WHERE course_id = ? AND user_id = ? AND order_id IS NULL");
    $stmt->bind_param("ii", $course_id, $user_id);
    $stmt->execute();
    $cart_item = $stmt->get_result()->fetch_assoc();

    if ($cart_item) {
        // Update quantity
        $stmt = $conn->prepare("UPDATE cart SET quantity = quantity + 1 WHERE course_id = ? AND user_id = ? AND order_id IS NULL");
        $stmt->bind_param("ii", $course_id, $user_id);
        $stmt->execute();
    } else {
        // Add to cart
        $stmt = $conn->prepare("INSERT INTO cart (user_id, course_id, price, quantity, created_at) VALUES (?, ?, ?, ?, NOW())");
        $stmt->bind_param("iiid", $user_id, $course_id, $price, $quantity);
        $stmt->execute();
    }

    // Redirect back to course page (or cart)
    header("Location: panier.php");
    exit;
} else {
    die("No course selected");
}
