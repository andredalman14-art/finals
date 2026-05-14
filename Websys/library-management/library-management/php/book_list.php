<?php
include 'db_connect.php';
include 'auth.php';
require_login();

$sql = "SELECT books.*, book_categories.category_name
        FROM books
        LEFT JOIN book_categories ON books.category_id = book_categories.category_id
        ORDER BY books.book_id DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Book List | DJ PHP</title>
  <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
  <header class="site-header">
    <a class="brand" href="dashboard.php"><span class="brand-mark">DJ</span><span>Library</span></a>
    <nav class="nav-links">
      <?php render_nav('books'); ?>
    </nav>
  </header>

  <main>
    <section class="page-hero">
      <p class="eyebrow">Catalog Management</p>
      <h1>Master Book Catalog</h1>
      <p class="page-subtitle">Browse, update, or remove titles from the library database.</p>
    </section>
    <section class="page-shell">
      <section class="table-panel">
        <div class="table-actions">
          <a class="primary-button" href="add_book.php">Add New Book</a>
        </div>
        <table>
          <thead>
            <tr>
              <th>ID</th>
              <th>Accession</th>
              <th>Title</th>
              <th>Author</th>
              <th>Category</th>
              <th>Copies</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if ($result && $result->num_rows > 0): ?>
              <?php while ($row = $result->fetch_assoc()): ?>
                <?php $status_class = strtolower($row['status']); ?>
                <tr>
                  <td><?php echo htmlspecialchars($row['book_id']); ?></td>
                  <td><?php echo htmlspecialchars($row['accession_number']); ?></td>
                  <td><?php echo htmlspecialchars($row['title']); ?></td>
                  <td><?php echo htmlspecialchars($row['author']); ?></td>
                  <td><?php echo htmlspecialchars($row['category_name']); ?></td>
                  <td><?php echo htmlspecialchars($row['copies']); ?></td>
                  <td><span class="pill <?php echo htmlspecialchars($status_class); ?>"><?php echo htmlspecialchars($row['status']); ?></span></td>
                  <td>
                    <div class="table-actions">
                      <a class="small-button" href="edit_book.php?id=<?php echo $row['book_id']; ?>">Edit</a>
                      <a class="small-button danger-button" href="delete_book.php?id=<?php echo $row['book_id']; ?>">Delete</a>
                    </div>
                  </td>
                </tr>
              <?php endwhile; ?>
            <?php else: ?>
              <tr><td colspan="8">No records found.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </section>
    </section>
  </main>
</body>
</html>
