<?php
include 'db_connect.php';
include 'auth.php';
require_login();

$result = $conn->query("SELECT * FROM borrowers ORDER BY borrower_id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Borrowers | DJ PHP</title>
  <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
  <header class="site-header">
    <a class="brand" href="dashboard.php"><span class="brand-mark">DJ</span><span>Library</span></a>
    <nav class="nav-links">
      <?php render_nav('borrowers'); ?>
    </nav>
  </header>

  <main>
    <section class="page-hero">
      <p class="eyebrow">Borrower Records</p>
      <h1>Borrowers</h1>
      <p class="page-subtitle">View and manage registered library borrowers.</p>
    </section>
    <section class="page-shell">
      <section class="table-panel">
        <table>
          <thead>
            <tr>
              <th>ID</th>
              <th>Borrower Code</th>
              <th>Name</th>
              <th>Course/Department</th>
              <th>Email</th>
              <th>Contact</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            <?php if ($result && $result->num_rows > 0): ?>
              <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                  <td><?php echo htmlspecialchars($row['borrower_id']); ?></td>
                  <td><?php echo htmlspecialchars($row['borrower_code']); ?></td>
                  <td><?php echo htmlspecialchars($row['full_name']); ?></td>
                  <td><?php echo htmlspecialchars($row['course_department']); ?></td>
                  <td><?php echo htmlspecialchars($row['email']); ?></td>
                  <td><?php echo htmlspecialchars($row['contact_number']); ?></td>
                  <td><span class="pill available"><?php echo htmlspecialchars($row['account_status']); ?></span></td>
                </tr>
              <?php endwhile; ?>
            <?php else: ?>
              <tr><td colspan="7">No borrower records found.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </section>
    </section>
  </main>
</body>
</html>
