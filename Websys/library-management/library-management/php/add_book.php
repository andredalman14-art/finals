<?php
include 'db_connect.php';
include 'auth.php';
require_login();

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
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
        $sql = "INSERT INTO books (accession_number, isbn, title, author, publisher, category_id, shelf_location, copies, status, remarks)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssissss", $accession_number, $isbn, $title, $author, $publisher, $category_id, $shelf_location, $copies, $status, $remarks);

        if ($stmt->execute()) {
            $message = "Book record inserted successfully.";
        } else {
            $message = "Unable to save record. Check if the accession number already exists.";
        }
    }
}

$categories = $conn->query("SELECT category_id, category_name FROM book_categories ORDER BY category_name");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Add Book | DJ PHP</title>
  <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
  <header class="site-header">
    <a class="brand" href="dashboard.php"><span class="brand-mark">DJ</span><span>Library</span></a>
    <nav class="nav-links">
      <?php render_nav('add_book'); ?>
    </nav>
  </header>

  <main>
    <section class="page-hero">
      <p class="eyebrow">Create operation</p>
      <h1>Add a book record</h1>
      <p class="page-subtitle">Ensure all required fields are completed accurately before saving.</p>
    </section>
    <section class="page-shell">
      <div class="form-panel">
        <?php if ($message != ""): ?><p class="alert"><?php echo htmlspecialchars($message); ?></p><?php endif; ?>
        <form method="POST" action="add_book.php">
          <div class="two-column">
            <div class="field-group">
              <label for="accession_number">Accession Number</label>
              <input id="accession_number" name="accession_number" type="text" required>
            </div>
            <div class="field-group">
              <label for="isbn">ISBN</label>
              <input id="isbn" name="isbn" type="text">
            </div>
          </div>
          <div class="field-group">
            <label for="title">Book Title</label>
            <input id="title" name="title" type="text" required>
          </div>
          <div class="two-column">
            <div class="field-group">
              <label for="author">Author</label>
              <input id="author" name="author" type="text" required>
            </div>
            <div class="field-group">
              <label for="publisher">Publisher</label>
              <input id="publisher" name="publisher" type="text">
            </div>
          </div>
          <div class="two-column">
            <div class="field-group">
              <label for="category_id">Category</label>
              <select id="category_id" name="category_id" required>
                <?php while ($category = $categories->fetch_assoc()): ?>
                  <option value="<?php echo $category['category_id']; ?>"><?php echo htmlspecialchars($category['category_name']); ?></option>
                <?php endwhile; ?>
              </select>
            </div>
            <div class="field-group">
              <label for="shelf_location">Shelf Location</label>
              <input id="shelf_location" name="shelf_location" type="text">
            </div>
          </div>
          <div class="two-column">
            <div class="field-group">
              <label for="copies">Copies</label>
              <input id="copies" name="copies" type="number" min="1" value="1" required>
            </div>
            <div class="field-group">
              <label for="status">Status</label>
              <select id="status" name="status">
                <option>Available</option>
                <option>Borrowed</option>
                <option>Reserved</option>
                <option>Overdue</option>
              </select>
            </div>
          </div>
          <div class="field-group">
            <label for="remarks">Remarks</label>
            <textarea id="remarks" name="remarks"></textarea>
          </div>
          <button class="primary-button" type="submit">Save Book</button>
        </form>
      </div>
    </section>
  </main>
</body>
</html>
