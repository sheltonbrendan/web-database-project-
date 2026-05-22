<?php
session_start();
require 'db.php';
require 'functions.php';
require_login();

$uid = $_SESSION['user_id'];
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch item
$stmt = $conn->prepare("SELECT title, description, image FROM items WHERE id = ? AND user_id = ?");
$stmt->bind_param('ii', $id, $uid);
$stmt->execute();
$res = $stmt->get_result();
$item = $res->fetch_assoc();
$stmt->close();

if (!$item) die('Item not found or not yours.');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) die('Invalid CSRF token');

    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $newImagePath = $item['image']; // Keep old image by default

    // Handle new image upload if provided
    if (!empty($_FILES['image']['name'])) {
        $targetDir = "uploads/";
        $fileName = time() . "_" . basename($_FILES["image"]["name"]);
        $targetFilePath = $targetDir . $fileName;
        $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($fileType, $allowedTypes)) {
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFilePath)) {
                // Delete old image if it exists
                if (!empty($item['image']) && file_exists("uploads/" . $item['image'])) {
                    unlink("uploads/" . $item['image']);
                }
                $newImagePath = $fileName;
            } else {
                $error = "Error uploading new image.";
            }
        } else {
            $error = "Only JPG, JPEG, PNG & GIF files are allowed.";
        }
    }

    if (empty($error) && $title !== '') {
        $upd = $conn->prepare("UPDATE items SET title = ?, description = ?, image = ? WHERE id = ? AND user_id = ?");
        $upd->bind_param('sssii', $title, $description, $newImagePath, $id, $uid);
        if ($upd->execute()) {
            header('Location: dashboard.php');
            exit;
        } else {
            $error = 'Database error: ' . $upd->error;
        }
        $upd->close();
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
  <title>Edit Item - simple_app</title>
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
    label {
      font-weight: 600;
      display: block;
      margin-top: 15px;
    }
    input, textarea {
      width: 100%;
      padding: 10px;
      border: 1px solid #ddd;
      border-radius: 6px;
      font-size: 15px;
      margin-top: 5px;
    }
    textarea { min-height: 120px; }
    .error { background:#ffe8e8; color:#900; padding:8px; border-radius:6px; margin-bottom:10px; }
    .preview {
      text-align: center;
      margin-top: 15px;
    }
    .preview img {
      max-width: 200px;
      max-height: 200px;
      border-radius: 8px;
      border: 1px solid #ddd;
    }
    .btns {
      text-align: center;
      margin-top: 20px;
    }
    button, a.btn {
      background: #2b7cff;
      color: #fff;
      border: none;
      padding: 10px 18px;
      border-radius: 6px;
      font-size: 15px;
      cursor: pointer;
      text-decoration: none;
      margin: 5px;
      transition: 0.2s ease;
    }
    button:hover, a.btn:hover {
      background: #195ed6;
      transform: translateY(-2px);
    }
  </style>
</head>
<body>
  <div class="form-container">
    <h1>Edit Item</h1>
    <?php if(!empty($error)) echo "<div class='error'>$error</div>"; ?>
    <form method="post" enctype="multipart/form-data">
      <input type="hidden" name="csrf_token" value="<?= esc($token) ?>">

      <label>Title</label>
      <input name="title" value="<?= esc($_POST['title'] ?? $item['title']) ?>" required>

      <label>Description</label>
      <textarea name="description"><?= esc($_POST['description'] ?? $item['description']) ?></textarea>

      <label>Current Image</label>
      <div class="preview">
        <?php if (!empty($item['image'])): ?>
          <img src="uploads/<?= esc($item['image']) ?>" alt="Current Image">
        <?php else: ?>
          <img src="assets/no-image.png" alt="No Image">
        <?php endif; ?>
      </div>

      <label>Upload New Image (optional)</label>
      <input type="file" name="image" accept="image/*">

      <div class="btns">
        <button type="submit">Save Changes</button>
        <a class="btn" href="dashboard.php">Cancel</a>
      </div>
    </form>
  </div>
</body>
</html>


