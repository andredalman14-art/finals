<?php
include 'db_connect.php';
include 'auth.php';

$message = "";
$message_class = "alert error";
$token = isset($_GET['token']) ? $_GET['token'] : (isset($_POST['token']) ? $_POST['token'] : "");
$valid_token = false;
$reset_user_id = 0;

$conn->query("CREATE TABLE IF NOT EXISTS password_resets (
    reset_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    token_hash CHAR(64) NOT NULL UNIQUE,
    expires_at DATETIME NOT NULL,
    used_at DATETIME NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
)");

if ($token != "") {
    $token_hash = hash('sha256', $token);
    $stmt = $conn->prepare("SELECT pr.reset_id, pr.user_id FROM password_resets pr WHERE pr.token_hash = ? AND pr.expires_at > NOW() AND pr.used_at IS NULL");
    $stmt->bind_param("s", $token_hash);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $reset = $result->fetch_assoc();
        $valid_token = true;
        $reset_user_id = (int) $reset['user_id'];
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if (!$valid_token) {
        $message = "This reset link is invalid or expired.";
    } elseif ($password == "" || $confirm_password == "") {
        $message = "Please complete both password fields.";
    } elseif ($password !== $confirm_password) {
        $message = "Passwords do not match.";
    } elseif (strlen($password) < 6) {
        $message = "Password must be at least 6 characters.";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $update = $conn->prepare("UPDATE users SET password = ? WHERE user_id = ?");
        $update->bind_param("si", $hashed_password, $reset_user_id);
        $update->execute();

        $token_hash = hash('sha256', $token);
        $mark_used = $conn->prepare("UPDATE password_resets SET used_at = NOW() WHERE token_hash = ?");
        $mark_used->bind_param("s", $token_hash);
        $mark_used->execute();

        $message = "Password changed successfully. You may now sign in.";
        $message_class = "alert";
        $valid_token = false;
    }
} elseif (!$valid_token) {
    $message = "This reset link is invalid or expired.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Reset Password | DJ PHP</title>
  <link rel="stylesheet" href="../css/styles.css?v=auth-split-4">
</head>
<body class="auth-page reset-page">
  <main class="auth-form-wrap auth-screen">
    <section class="form-panel narrow auth-card">
      <h1>Reset Password</h1>
      <?php if ($message != ""): ?><p class="<?php echo $message_class; ?>"><?php echo htmlspecialchars($message); ?></p><?php endif; ?>
      <?php if ($valid_token): ?>
      <form method="POST" action="reset_password.php">
        <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
        <div class="field-group">
          <label for="password">New Password</label>
          <input id="password" name="password" type="password" placeholder="New Password" required>
        </div>
        <div class="field-group">
          <label for="confirm_password">Confirm Password</label>
          <input id="confirm_password" name="confirm_password" type="password" placeholder="Confirm Password" required>
        </div>
        <button class="primary-button" type="submit">CHANGE PASSWORD</button>
      </form>
      <?php else: ?>
      <a class="primary-button auth-link-button" href="forgot_password.php">SEND NEW LINK</a>
      <?php endif; ?>
    </section>

    <section class="auth-panel">
      <div>
        <h2>Almost There</h2>
        <p>Create a new password and return to your library dashboard.</p>
        <a class="ghost-button" href="login.php">SIGN IN</a>
      </div>
    </section>
  </main>
</body>
</html>
