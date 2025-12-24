<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch current user info
$stmt = $conn->prepare("SELECT * FROM users WHERE id=?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$currentUser = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$currentUser) die("User not found");

// Get submitted data
$firstname    = trim($_POST['firstname'] ?? '');
$lastname     = trim($_POST['lastname'] ?? '');
$phone_number = trim($_POST['phone_number'] ?? '');
$address      = trim($_POST['address'] ?? '');
$telegram     = trim($_POST['telegram'] ?? '');
$bio          = trim($_POST['bio'] ?? '');

// Track fields to update
$fields = [];
$params = [];
$types = '';

// Compare and add changed fields
if ($firstname !== $currentUser['firstname']) {
    $fields[] = "firstname=?";
    $params[] = $firstname;
    $types .= 's';
}
if ($lastname !== $currentUser['lastname']) {
    $fields[] = "lastname=?";
    $params[] = $lastname;
    $types .= 's';
}
if ($phone_number !== $currentUser['phone_number']) {
    $fields[] = "phone_number=?";
    $params[] = $phone_number;
    $types .= 's';
}
if ($address !== $currentUser['address']) {
    $fields[] = "address=?";
    $params[] = $address;
    $types .= 's';
}
if ($telegram !== $currentUser['telegram']) {
    $fields[] = "telegram=?";
    $params[] = $telegram;
    $types .= 's';
}
if ($bio !== $currentUser['bio']) {
    $fields[] = "bio=?";
    $params[] = $bio;
    $types .= 's';
}

// Handle profile picture
if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === 0) {
    $uploadDir = 'uploads/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

    $filename = time() . "_" . basename($_FILES['profile_picture']['name']);
    $targetPath = $uploadDir . $filename;

    if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $targetPath)) {
        $fields[] = "profile_picture=?";
        $params[] = $targetPath;
        $types .= 's';

        // Optionally delete old profile picture
        if (!empty($currentUser['profile_picture']) && file_exists($currentUser['profile_picture'])) {
            unlink($currentUser['profile_picture']);
        }
    }
}

// If nothing changed
if (empty($fields)) {
    header("Location: profile.php");
    exit;
}

// Build SQL
$sql = "UPDATE users SET " . implode(", ", $fields) . " WHERE id=?";
$params[] = $user_id;
$types .= 'i';

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);

if ($stmt->execute()) {
    header("Location: profile.php");
    exit;
} else {
    die("Error updating profile: " . $stmt->error);
}

?>