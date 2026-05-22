<?php
session_start();
require 'db.php';
require 'functions.php';

// Fetch all items with owner info
$sql = "SELECT items.id, items.title, items.description, items.image, items.created_at, users.username
        FROM items
        JOIN users ON items.user_id = users.id
        ORDER BY items.created_at DESC";
$res = $conn->query($sql);
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Public Items - simple_app</title>
  <link rel="stylesheet" href="assets/style.css">
  <style>
    body {
      margin:0;
      font-family:"Segoe UI", Roboto, Arial, sans-serif;
      background:linear-gradient(135deg, #2b7cff, #6fb1fc);
      min-height:100vh;
      display:flex; flex-direction:column; align-items:center;
    }
    .items-container {
      background:#fff;
      width:90%;
      max-width:950px;
      margin-top:60px;
      padding:30px 40px;
      border-radius:12px;
      box-shadow:0 8px 25px rgba(0,0,0,0.1);
    }
    header {
      display:flex; justify-content:space-between; align-items:center;
      margin-bottom:25px;
    }
    header h1 { color:#2b7cff; margin:0; font-size:26px; }
    .nav-links a {
      background:#2b7cff; color:#fff; padding:8px 14px; border-radius:6px;
      text-decoration:none; margin-left:8px; font-weight:500; transition:0.2s ease;
    }
    .nav-links a:hover { background:#195ed6; transform:translateY(-2px); }
    .item-card {
      background:#f9f9f9;
      border:1px solid #eee;
      border-radius:8px;
      padding:15px 20px;
      margin-bottom:15px;
      box-shadow:0 2px 6px rgba(0,0,0,0.05);
      display:flex;
      gap:15px;
      align-items:flex-start;
    }
    .item-card img {
      width:120px;
      height:120px;
      object-fit:cover;
      border-radius:8px;
      border:1px solid #ddd;
    }
    .item-content {
      flex:1;
    }
    .item-content h3 { margin:0 0 5px 0; color:#2b7cff; }
    .item-content p { color:#555; margin-bottom:10px; }
    small { color:#777; }
  </style>
</head>
<body>
  <div class="items-container">
    <header>
      <h1>Public Items</h1>
      <div class="nav-links">
        <?php if (isset($_SESSION['user_id'])): ?>
          <a href="dashboard.php">Dashboard</a>
          <a href="logout.php">Logout</a>
        <?php else: ?>
          <a href="login.php">Login</a>
          <a href="register.php">Register</a>
        <?php endif; ?>
      </div>
    </header>

    <?php if ($res->num_rows === 0): ?>
      <p>No items have been posted yet.</p>
    <?php else: ?>
      <?php while ($row = $res->fetch_assoc()): ?>
        <div class="item-card">
          <?php if ($row['image']): ?>
            <img src="uploads/<?= esc($row['image']) ?>" alt="<?= esc($row['title']) ?>">
          <?php else: ?>
            <img src="assets/no-image.png" alt="No image available">
          <?php endif; ?>

          <div class="item-content">
            <h3><?= esc($row['title']) ?></h3>
            <p><?= nl2br(esc($row['description'])) ?></p>
            <small>Posted by <b><?= esc($row['username']) ?></b> on <?= esc($row['created_at']) ?></small>
          </div>
        </div>
      <?php endwhile; ?>
    <?php endif; ?>
  </div>
</body>
</html>

