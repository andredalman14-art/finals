<?php
include 'db_connect.php';
include 'auth.php';
require_login();

$message = "";
$book_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $book_id = intval($_POST['book_id']);
    $accession_number = trim($_POST['accession_number']);
    $isbn = trim($_POST['isbn']);
    $title = trim($_POST['title']);
    $author = trim($_POST['author']);
    $publisher = trim($_POST['publisher']);
    $category_id = $_POST['category_id'];
    $shelf_location = trim($_POST['shelf_location']);
    $copies = $_POST['copies'];
    $status = $_POST['status'];
    $remarks = trim($_POST['remarks']);

    if ($accession_number == "" || $title == "" || $author == "" || $copies < 1) {
        $message = "Please complete the required fields correctly.";
    } else {
        $sql = "UPDATE books
                SET accession_number = ?, isbn = ?, title = ?, author = ?, publisher = ?, category_id = ?,
                    shelf_location = ?, copies = ?, status = ?, remarks = ?
                WHERE book_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssissssi", $accession_number, $isbn, $title, $author, $publisher, $category_id, $shelf_location, $copies, $status, $remarks, $book_id);

        if ($stmt->execute()) {
            $message = "Book record updated successfully.";
        } else {
            $message = "Update failed. Check the accession number.";
        }
    }
}

$stmt = $conn->prepare("SELECT * FROM books WHERE book_id = ?");
$stmt->bind_param("i", $book_id);
$stmt->execute();
$book = $stmt->get_result()->fetch_assoc();

if (!$book) {
    die("Book record not found.");
}

$categories = $conn->query("SELECT category_id, category_name FROM book_categories ORDER BY category_name");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Book | DJ PHP</title>
  <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
  <header class="site-header">
    <a class="brand" href="dashboard.php"><span class="brand-mark">DJ</span><span>ShelfWise PHP</span></a>
    <nav class="nav-links">
      <?php render_nav('edit_book'); ?>
    </nav>
  </header>

  <main>
    <section class="page-hero">
      <p class="eyebrow">Update operation</p>
      <h1>Edit book record</h1>
    </section>
    <section class="page-shell">
      <div class="form-panel">
        <?php if ($message != ""): ?><p class="alert"><?php echo htmlspecialchars($message); ?></p><?php endif; ?>
        <form method="POST" action="edit_book.php?id=<?php echo $book_id; ?>">
          <input type="hidden" name="book_id" value="<?php echo htmlspecialchars($book['book_id']); ?>">
          <div class="two-column">
            <div class="field-group">
              <label for="accession_number">Accession Number</label>
              <input id="accession_number" name="accession_number" type="text" value="<?php echo htmlspecialchars($book['accession_number']); ?>" required>
            </div>
            <div class="field-group">
              <label for="isbn">ISBN</label>
              <input id="isbn" name="isbn" type="text" value="<?php echo htmlspecialchars($book['isbn']); ?>">
            </div>
          </div>
          <div class="field-group">
            <label for="title">Book Title</label>
            <input id="title" name="title" type="text" value="<?php echo htmlspecialchars($book['title']); ?>" required>
          </div>
          <div class="two-column">
            <div class="field-group">
              <label for="author">Author</label>
              <input id="author" name="author" type="text" value="<?php echo htmlspecialchars($book['author']); ?>" required>
            </div>
            <div class="field-group">
              <label for="publisher">Publisher</label>
              <input id="publisher" name="publisher" type="text" value="<?php echo htmlspecialchars($book['publisher']); ?>">
            </div>
          </div>
          <div class="two-column">
            <div class="field-group">
              <label for="category_id">Category</label>
              <select id="category_id" name="category_id" required>
                <?php while ($category = $categories->fetch_assoc()): ?>
                  <option value="<?php echo $category['category_id']; ?>" <?php if ($category['category_id'] == $book['category_id']) echo "selected"; ?>>
                    <?php echo htmlspecialchars($category['category_name']); ?>
                  </option>
                <?php endwhile; ?>
              </select>
            </div>
            <div class="field-group">
              <label for="dj_location">DJ Location</label>
              <input id="dj_location" name="dj_location" type="text" value="<?php echo htmlspecialchars($book['shelf_location']); ?>">
            </div>
          </div>
          <div class="two-column">
            <div class="field-group">
              <label for="copies">Copies</label>
              <input id="copies" name="copies" type="number" min="1" value="<?php echo htmlspecialchars($book['copies']); ?>" required>
            </div>
            <div class="field-group">
              <label for="status">Status</label>
              <select id="status" name="status">
                <?php foreach (['Available', 'Borrowed', 'Reserved', 'Overdue'] as $status): ?>
                  <option <?php if ($status == $book['status']) echo "selected"; ?>><?php echo $status; ?></option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>
          <div class="field-group">
            <label for="remarks">Remarks</label>
            <textarea id="remarks" name="remarks"><?php echo htmlspecialchars($book['remarks']); ?></textarea>
          </div>
          <button class="primary-button" type="submit">Update Book</button>
          <a class="secondary-button" href="book_list.php">Back to Book List</a>
        </form>
      </div>
    </section>
  </main>
</body>
</html>
