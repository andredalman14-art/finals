<?php
include 'auth.php';
require_login();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Profile | DJ PHP</title>
  <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
  <header class="site-header">
    <a class="brand" href="dashboard.php"><span class="brand-mark">DJ</span><span>Library</span></a>
    <nav class="nav-links">
      <?php render_nav('profile'); ?>
    </nav>
  </header>

  <main>
    <section class="page-hero">
      <p class="eyebrow">User Credentials</p>
      <h1>Staff Information Dashboard</h1>
      <p class="page-subtitle">Account details and current session information.</p>
    </section>

    <section class="page-shell profile-layout">
      <aside class="profile-panel">
        <div class="profile-photo">DJ</div>
        <h2><?php echo htmlspecialchars(current_user_name()); ?></h2>
        <p class="muted"><?php echo htmlspecialchars($_SESSION['role']); ?></p>
        <ul class="profile-list">
          <li><span>User ID</span><strong><?php echo htmlspecialchars($_SESSION['user_id']); ?></strong></li>
          <li><span>Role</span><strong><?php echo htmlspecialchars($_SESSION['role']); ?></strong></li>
          <li><span>Session</span><strong>Active</strong></li>
        </ul>
      </aside>
    </section>
  </main>
</body>
</html>
