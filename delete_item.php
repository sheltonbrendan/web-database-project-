<?php
session_start();
require 'db.php';
require 'functions.php';
require_login();

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: dashboard.php');
    exit;
}

// Validate CSRF token
if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
    die('Invalid CSRF token');
}

$id = (int)($_POST['id'] ?? 0);
$uid = $_SESSION['user_id'];

// Fetch the item's image filename first
$stmt = $conn->prepare("SELECT image FROM items WHERE id = ? AND user_id = ?");
$stmt->bind_param('ii', $id, $uid);
$stmt->execute();
$res = $stmt->get_result();
$item = $res->fetch_assoc();
$stmt->close();

if (!$item) {
    $deleted = false;
} else {
    // Delete the item from the database
    $stmt = $conn->prepare("DELETE FROM items WHERE id = ? AND user_id = ?");
    $stmt->bind_param('ii', $id, $uid);
    $stmt->execute();
    $deleted = $stmt->affected_rows > 0;
    $stmt->close();

    // If deletion successful and image exists, delete it from /uploads/
    if ($deleted && !empty($item['image']) && file_exists("uploads/" . $item['image'])) {
        unlink("uploads/" . $item['image']);
    }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Delete Item - simple_app</title>
  <link rel="stylesheet" href="assets/style.css">
  <style>
    body {
      margin: 0;
      font-family: "Segoe UI", Roboto, Arial, sans-serif;
      background: linear-gradient(135deg, #2b7cff, #6fb1fc);
      display: flex;
      align-items: center;
      justify-content: center;
      min-height: 100vh;
      color: #333;
    }
    .message-box {
      background: #fff;
      width: 90%;
      max-width: 500px;
      border-radius: 12px;
      box-shadow: 0 8px 25px rgba(0,0,0,0.1);
      padding: 40px;
      text-align: center;
    }
    h1 {
      color: #2b7cff;
      margin-bottom: 15px;
    }
    .success {
      color: #0a8a2f;
      background: #e6ffea;
      padding: 10px;
      border-radius: 6px;
    }
    .error {
      color: #900;
      background: #ffe8e8;
      padding: 10px;
      border-radius: 6px;
    }
    a.btn {
      display: inline-block;
      margin-top: 20px;
      padding: 10px 18px;
      background: #2b7cff;
      color: #fff;
      border-radius: 6px;
      text-decoration: none;
      transition: 0.2s ease;
    }
    a.btn:hover {
      background: #195ed6;
      transform: translateY(-2px);
    }
  </style>
</head>
<body>
  <div class="message-box">
    <h1>Delete Item</h1>
    <?php if ($deleted): ?>
      <div class="success">The item and its image were deleted successfully.</div>
    <?php else: ?>
      <div class="error">Item not found or could not be deleted.</div>
    <?php endif; ?>
    <a class="btn" href="dashboard.php">Back to Dashboard</a>
  </div>
</body>
</html>

