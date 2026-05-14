<?php
include 'db_connect.php';
include 'auth.php';

$total_books = $conn->query("SELECT COUNT(*) AS total FROM books")->fetch_assoc()['total'];
$available_books = $conn->query("SELECT COUNT(*) AS total FROM books WHERE status = 'Available'")->fetch_assoc()['total'];
$borrowed_books = $conn->query("SELECT COUNT(*) AS total FROM books WHERE status = 'Borrowed'")->fetch_assoc()['total'];
$overdue_books = $conn->query("SELECT COUNT(*) AS total FROM books WHERE status = 'Overdue'")->fetch_assoc()['total'];
$total_borrowers = $conn->query("SELECT COUNT(*) AS total FROM borrowers")->fetch_assoc()['total'];
$recent = $conn->query("SELECT borrowers.full_name, books.title, borrow_transactions.date_borrowed, borrow_transactions.due_date, borrow_transactions.transaction_status
                        FROM borrow_transactions
                        INNER JOIN borrowers ON borrow_transactions.borrower_id = borrowers.borrower_id
                        INNER JOIN books ON borrow_transactions.book_id = books.book_id
                        ORDER BY borrow_transactions.transaction_id DESC
                        LIMIT 5");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard | DJ Library</title>
  <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
  <header class="site-header">
    <a class="brand" href="dashboard.php"><span class="brand-mark">DJ</span><span>Library</span></a>
    <nav class="nav-links">
      <?php render_nav('dashboard'); ?>
    </nav>
  </header>

  <main>
    <section class="page-hero">
      <h1><?php echo is_logged_in() ? 'Welcome, ' . htmlspecialchars(current_user_name()) : 'Library Dashboard'; ?></h1>
      <p class="page-subtitle">
        <?php echo is_logged_in() ? 'Manage library records.' : 'Log in to manage books and borrowers.'; ?>
      </p>
    </section>
    <section class="page-shell">
      <div class="stats-grid">
        <article class="stat-card"><span>Total Books</span><strong><?php echo $total_books; ?></strong></article>
        <article class="stat-card"><span>Borrowers</span><strong><?php echo $total_borrowers; ?></strong></article>
        <article class="stat-card"><span>Borrowed</span><strong><?php echo $borrowed_books; ?></strong></article>
        <article class="stat-card"><span>Overdue</span><strong><?php echo $overdue_books; ?></strong></article>
      </div>
      <div class="dashboard-layout">
        <section class="table-panel">
          <h2>Recent Borrowing Activity</h2>
          <table>
            <thead>
              <tr>
                <th>Borrower</th>
                <th>Book Title</th>
                <th>Date Borrowed</th>
                <th>Due Date</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              <?php if ($recent && $recent->num_rows > 0): ?>
                <?php while ($row = $recent->fetch_assoc()): ?>
                  <tr>
                    <td><?php echo htmlspecialchars($row['full_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['title']); ?></td>
                    <td><?php echo htmlspecialchars($row['date_borrowed']); ?></td>
                    <td><?php echo htmlspecialchars($row['due_date']); ?></td>
                    <td><span class="pill <?php echo strtolower(htmlspecialchars($row['transaction_status'])); ?>"><?php echo htmlspecialchars($row['transaction_status']); ?></span></td>
                  </tr>
                <?php endwhile; ?>
              <?php else: ?>
                <tr><td colspan="5">No borrowing activity found.</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </section>
        <aside class="activity-panel">
          <h2><?php echo is_logged_in() ? 'Management Tools' : 'Next Step'; ?></h2>
          <ul class="activity-list">
            <?php if (is_logged_in()): ?>
              <li><a href="book_list.php"><strong>Manage books</strong><br><span class="muted">Create, read, update, and delete book records.</span></a></li>
              <li><a href="borrowers.php"><strong>View borrowers</strong><br><span class="muted">Review registered borrower records.</span></a></li>
              <li><a href="status.php"><strong>Check status</strong><br><span class="muted">See availability and borrowing status.</span></a></li>
            <?php else: ?>
              <li><a href="login.php"><strong>Login to manage records</strong><br><span class="muted">Books, borrowers, add-book, and profile pages appear after login.</span></a></li>
            <?php endif; ?>
          </ul>
        </aside>
      </div>
    </section>
  </main>
</body>
</html>
