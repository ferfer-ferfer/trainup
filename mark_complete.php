<?php
session_start();
require 'db.php'; // must define $conn (mysqli)

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = (int) $_SESSION['user_id'];

// Check POST data
if (!isset($_POST['module_id'], $_POST['course_id'])) {
    die("Invalid request");
}

$module_id = (int) $_POST['module_id'];
$course_id = (int) $_POST['course_id'];

/* 1️⃣ Check if module already exists for this user */
$check = $conn->prepare("
    SELECT id FROM user_modules
    WHERE user_id = ? AND module_id = ?
");
$check->bind_param("ii", $user_id, $module_id);
$check->execute();
$check->store_result();

if ($check->num_rows > 0) {
    /* 2️⃣ Update existing record */
    $update = $conn->prepare("
        UPDATE user_modules
        SET completed = 1, completed_at = NOW()
        WHERE user_id = ? AND module_id = ?
    ");
    $update->bind_param("ii", $user_id, $module_id);
    $update->execute();
} else {
    /* 3️⃣ Insert new record */
    $insert = $conn->prepare("
        INSERT INTO user_modules (user_id, module_id, completed, completed_at)
        VALUES (?, ?, 1, NOW())
    ");
    $insert->bind_param("ii", $user_id, $module_id);
    $insert->execute();
}

/* 4️⃣ Redirect back to course page */
header("Location: my_courses.php");
exit;
