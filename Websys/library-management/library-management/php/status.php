<?php
include 'db_connect.php';
include 'auth.php';

$available = $conn->query("SELECT COUNT(*) AS total FROM books WHERE status = 'Available'")->fetch_assoc()['total'];
$borrowed = $conn->query("SELECT COUNT(*) AS total FROM books WHERE status = 'Borrowed'")->fetch_assoc()['total'];
$overdue = $conn->query("SELECT COUNT(*) AS total FROM books WHERE status = 'Overdue'")->fetch_assoc()['total'];
$reserved = $conn->query("SELECT COUNT(*) AS total FROM books WHERE status = 'Reserved'")->fetch_assoc()['total'];

$records = $conn->query("SELECT books.title, books.status, borrowers.full_name, borrow_transactions.due_date, borrow_transactions.transaction_status
                         FROM books
                         LEFT JOIN borrow_transactions ON books.book_id = borrow_transactions.book_id AND borrow_transactions.date_returned IS NULL
                         LEFT JOIN borrowers ON borrow_transactions.borrower_id = borrowers.borrower_id
                         ORDER BY books.title");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Status | DJ PHP</title>
  <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
  <header class="site-header">
    <a class="brand" href="dashboard.php"><span class="brand-mark">DJ</span><span>Library</span></a>
    <nav class="nav-links">
      <?php render_nav('status'); ?>
    </nav>
  </header>

  <main>
    <section class="page-hero">
      <p class="eyebrow">Circulation Analytics</p>
      <h1>Collection Circulation Overview</h1>
      <p class="page-subtitle">An automated overview of current inventory levels and circulation status</p>
    </section>

    <section class="page-shell">
      <div class="status-grid">
        <article class="status-card"><span>Available</span><strong><?php echo $available; ?></strong><p>Ready for borrowing</p></article>
        <article class="status-card"><span>Borrowed</span><strong><?php echo $borrowed; ?></strong><p>Currently checked out</p></article>
        <article class="status-card"><span>Overdue</span><strong><?php echo $overdue; ?></strong><p>Need librarian follow-up</p></article>
        <article class="status-card"><span>Reserved</span><strong><?php echo $reserved; ?></strong><p>Waiting for pickup</p></article>
      </div>

      <section class="table-panel">
        <h2>Status Details</h2>
        <table>
          <thead>
            <tr>
              <th>Book</th>
              <th>Borrower</th>
              <th>Current Status</th>
              <th>Due Date</th>
              <th>Next Action</th>
            </tr>
          </thead>
          <tbody>
            <?php if ($records && $records->num_rows > 0): ?>
              <?php while ($row = $records->fetch_assoc()): ?>
                <?php
                  $status_class = strtolower($row['status']);
                  $next_action = 'Ready on shelf';
                  if ($row['status'] == 'Borrowed') {
                      $next_action = 'Wait for return';
                  } elseif ($row['status'] == 'Overdue') {
                      $next_action = 'Send reminder';
                  } elseif ($row['status'] == 'Reserved') {
                      $next_action = 'Prepare pickup';
                  }
                ?>
                <tr>
                  <td><?php echo htmlspecialchars($row['title']); ?></td>
                  <td><?php echo htmlspecialchars($row['full_name'] ? $row['full_name'] : 'None'); ?></td>
                  <td><span class="pill <?php echo htmlspecialchars($status_class); ?>"><?php echo htmlspecialchars($row['status']); ?></span></td>
                  <td><?php echo htmlspecialchars($row['due_date'] ? $row['due_date'] : 'None'); ?></td>
                  <td><?php echo htmlspecialchars($next_action); ?></td>
                </tr>
              <?php endwhile; ?>
            <?php else: ?>
              <tr><td colspan="5">No status records found.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </section>
    </section>
  </main>
</body>
</html>
