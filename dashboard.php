<?php
session_start();
require 'db.php';
require 'functions.php';
require_login();

$uid = $_SESSION['user_id'];
$username = $_SESSION['username'];

// Fetch user's items
$stmt = $conn->prepare("SELECT id, title, description, image, created_at, updated_at FROM items WHERE user_id = ? ORDER BY created_at DESC");
$stmt->bind_param('i', $uid);
$stmt->execute();
$res = $stmt->get_result();
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Dashboard - simple_app</title>
  <link rel="stylesheet" href="assets/style.css">
  <style>
    body {
      margin: 0;
      font-family: "Segoe UI", Roboto, Arial, sans-serif;
      background: linear-gradient(135deg, #2b7cff, #6fb1fc);
      min-height: 100vh;
      color: #333;
      display: flex;
      flex-direction: column;
      align-items: center;
    }

    .dashboard-container {
      background: #fff;
      width: 90%;
      max-width: 950px;
      margin-top: 60px;
      padding: 30px 40px;
      border-radius: 12px;
      box-shadow: 0 8px 25px rgba(0,0,0,0.1);
    }

    header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 25px;
    }

    header h1 {
      color: #2b7cff;
      margin: 0;
      font-size: 26px;
    }

    .user-info {
      font-size: 15px;
      color: #333;
    }

    .user-info a {
      color: #2b7cff;
      text-decoration: none;
      font-weight: 500;
      margin-left: 10px;
    }

    .user-info a:hover {
      text-decoration: underline;
    }

    .create-btn {
      display: inline-block;
      background: #2b7cff;
      color: #fff;
      padding: 8px 15px;
      border-radius: 6px;
      text-decoration: none;
      font-weight: 500;
      transition: 0.2s ease;
    }

    .create-btn:hover {
      background: #195ed6;
      transform: translateY(-2px);
    }

    .item-list {
      margin-top: 20px;
    }

    .item-card {
      background: #f9f9f9;
      border: 1px solid #eee;
      border-radius: 8px;
      padding: 15px 20px;
      margin-bottom: 15px;
      box-shadow: 0 2px 6px rgba(0,0,0,0.05);
      display: flex;
      gap: 15px;
      align-items: flex-start;
    }

    .item-card img {
      width: 120px;
      height: 120px;
      object-fit: cover;
      border-radius: 8px;
      border: 1px solid #ddd;
    }

    .item-content {
      flex: 1;
    }

    .item-content h3 {
      margin: 0 0 5px 0;
      color: #2b7cff;
    }

    .item-content p {
      color: #555;
      margin-bottom: 10px;
    }

    small {
      color: #777;
    }

    .item-actions {
      margin-top: 8px;
    }

    .item-actions a,
    .item-actions button {
      font-size: 14px;
      background: none;
      border: none;
      color: #2b7cff;
      cursor: pointer;
      text-decoration: none;
      margin-right: 10px;
      font-weight: 500;
      transition: 0.2s ease;
    }

    .item-actions a:hover,
    .item-actions button:hover {
      text-decoration: underline;
    }

    footer {
      margin-top: 40px;
      text-align: center;
      color: #fff;
      font-size: 14px;
      padding: 10px;
    }
  </style>
</head>
<body>
  <div class="dashboard-container">
    <header>
      <h1>Dashboard</h1>
      <div class="user-info">
        Welcome, <b><?= esc($username) ?></b> |
        <a href="logout.php">Logout</a> |
        <a href="items.php">View Public Items</a>
      </div>
    </header>

    <a href="create_item.php" class="create-btn">+ Create New Item</a>

    <div class="item-list">
      <h2>Your Items</h2>
      <?php if ($res->num_rows === 0): ?>
        <p>You haven’t added any items yet.</p>
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
              <small>
                Created: <?= esc($row['created_at']) ?>
                <?= $row['updated_at'] ? ' | Updated: '.esc($row['updated_at']) : '' ?>
              </small>

              <div class="item-actions">
                <a href="edit_item.php?id=<?= (int)$row['id'] ?>">Edit</a>
                <form method="post" action="delete_item.php" style="display:inline;">
                  <input type="hidden" name="id" value="<?= (int)$row['id'] ?>">
                  <input type="hidden" name="csrf_token" value="<?= esc(get_csrf_token()) ?>">
                  <button type="submit" onclick="return confirm('Delete this item?')">Delete</button>
                </form>
              </div>
            </div>
          </div>
        <?php endwhile; ?>
      <?php endif; ?>
    </div>
  </div>

  <footer>
    <p>simple_app &copy; <?= date('Y') ?> | PHP & MySQL CRUD Project</p>
  </footer>
</body>
</html>

