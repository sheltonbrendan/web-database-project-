<?php
session_start();
require 'db.php';
require 'functions.php';
require_login();

$uid = $_SESSION['user_id'];
$username = $_SESSION['username'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) die('Invalid CSRF token');

    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $imagePath = null;

    // Handle image upload
    if (!empty($_FILES['image']['name'])) {
        $targetDir = "uploads/";
        $fileName = time() . "_" . basename($_FILES["image"]["name"]);
        $targetFilePath = $targetDir . $fileName;
        $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));

        // Allow certain file formats
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array($fileType, $allowedTypes)) {
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFilePath)) {
                $imagePath = $fileName;
            } else {
                $error = "Sorry, there was an error uploading your image.";
            }
        } else {
            $error = "Only JPG, JPEG, PNG & GIF files are allowed.";
        }
    }

    if (empty($error) && $title !== '') {
        $stmt = $conn->prepare("INSERT INTO items (user_id, title, description, image) VALUES (?, ?, ?, ?)");
        $stmt->bind_param('isss', $uid, $title, $description, $imagePath);
        if ($stmt->execute()) {
            header('Location: dashboard.php');
            exit;
        } else {
            $error = 'Database error: ' . $stmt->error;
        }
        $stmt->close();
    } elseif ($title === '') {
        $error = 'Title is required.';
    }
}
$token = get_csrf_token();
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Create Item - simple_app</title>
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
    }
    .form-container {
      background: #fff;
      width: 90%;
      max-width: 600px;
      border-radius: 12px;
      box-shadow: 0 8px 25px rgba(0,0,0,0.1);
      padding: 40px;
    }
    h1 {
      color: #2b7cff;
      text-align: center;
      margin-bottom: 25px;
    }
    label { font-weight: 600; display:block; margin-top:15px; }
    input, textarea {
      width:100%; padding:10px; border:1px solid #ddd; border-radius:6px;
      font-size:15px; margin-top:5px;
    }
    textarea { min-height:120px; }
    .error { background:#ffe8e8; color:#900; padding:8px; border-radius:6px; margin-bottom:10px; }
    .btns { text-align:center; margin-top:20px; }
    button, a.btn {
      background:#2b7cff; color:#fff; border:none; padding:10px 18px;
      border-radius:6px; font-size:15px; cursor:pointer; text-decoration:none;
      margin:5px; transition:0.2s ease;
    }
    button:hover, a.btn:hover { background:#195ed6; transform:translateY(-2px); }
  </style>
</head>
<body>
  <div class="form-container">
    <h1>Create New Item</h1>
    <?php if(!empty($error)) echo "<div class='error'>$error</div>"; ?>
    <form method="post" enctype="multipart/form-data">
      <input type="hidden" name="csrf_token" value="<?= esc($token) ?>">
      <label>Title</label>
      <input name="title" value="<?= esc($_POST['title'] ?? '') ?>" required>

      <label>Description</label>
      <textarea name="description"><?= esc($_POST['description'] ?? '') ?></textarea>

      <label>Image (optional)</label>
      <input type="file" name="image" accept="image/*">

      <div class="btns">
        <button type="submit">Create</button>
        <a class="btn" href="dashboard.php">Cancel</a>
      </div>
    </form>
  </div>
</body>
</html>

