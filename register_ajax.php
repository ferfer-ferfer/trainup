<?php
session_start();
require 'db.php';
require 'phpqrcode/qrlib.php';

// Check login
if(!isset($_SESSION['user_id'])){
    echo json_encode(['error' => 'You must be logged in.']);
    exit;
}

$user_id = $_SESSION['user_id'];
$event_id = isset($_POST['event_id']) ? (int)$_POST['event_id'] : 0;

if(!$event_id){
    echo json_encode(['error' => 'Invalid event.']);
    exit;
}

// Check if already registered
$stmt = $conn->prepare("SELECT qr_code FROM event_registrations WHERE user_id=? AND event_id=?");
$stmt->bind_param("ii", $user_id, $event_id);
$stmt->execute();
$res = $stmt->get_result();

if($res->num_rows > 0){
    $row = $res->fetch_assoc();
    echo json_encode([
        'qr' => $row['qr_code'],
        'message' => 'You are already registered!'
    ]);
    exit;
}

// Generate QR code
$code = $user_id . '-' . $event_id . '-' . time();
$path = "qrcodes/";
if(!file_exists($path)) mkdir($path, 0777, true);

$qr_file = $path . $code . ".png";
QRcode::png($code, $qr_file, QR_ECLEVEL_L, 6);

// Save registration
$stmt2 = $conn->prepare("INSERT INTO event_registrations(user_id,event_id,qr_code) VALUES(?,?,?)");
$stmt2->bind_param("iis", $user_id, $event_id, $qr_file);
$stmt2->execute();

echo json_encode([
    'qr' => $qr_file,
    'message' => 'Registration successful!'
]);
exit;
