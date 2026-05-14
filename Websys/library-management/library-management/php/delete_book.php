<?php
include 'db_connect.php';
include 'auth.php';
require_login();

$book_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $book_id = intval($_POST['book_id']);
    $stmt = $conn->prepare("DELETE FROM books WHERE book_id = ?");
    $stmt->bind_param("i", $book_id);

    if ($stmt->execute()) {
        header("Location: book_list.php");
        exit;
    }

    $message = "Delete failed. Please try again.";
}

$stmt = $conn->prepare("SELECT book_id, accession_number, title, author FROM books WHERE book_id = ?");
$stmt->bind_param("i", $book_id);
$stmt->execute();
$book = $stmt->get_result()->fetch_assoc();

if (!$book) {
    die("Book record not found.");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Delete Book | DJ PHP</title>
  <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
  <header class="site-header">
    <a class="brand" href="dashboard.php"><span class="brand-mark">DJ</span><span>ShelfWise PHP</span></a>
    <nav class="nav-links">
      <?php render_nav('delete_book'); ?>
    </nav>
  </header>

  <main class="auth-form-wrap">
    <section class="form-panel narrow">
      <p class="eyebrow">Delete operation</p>
      <h1>Delete this book?</h1>
      <?php if ($message != ""): ?><p class="alert error"><?php echo htmlspecialchars($message); ?></p><?php endif; ?>
      <p><strong><?php echo htmlspecialchars($book['title']); ?></strong></p>
      <p class="muted">
        Accession No. <?php echo htmlspecialchars($book['accession_number']); ?><br>
        Author: <?php echo htmlspecialchars($book['author']); ?>
      </p>
      <form method="POST" action="delete_book.php?id=<?php echo $book_id; ?>">
        <input type="hidden" name="book_id" value="<?php echo htmlspecialchars($book['book_id']); ?>">
        <button class="primary-button danger-solid" type="submit">Confirm Delete</button>
        <a class="secondary-button" href="book_list.php">Cancel</a>
      </form>
    </section>
  </main>
</body>
</html>
