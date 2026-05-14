<?php
include 'db_connect.php';
include 'auth.php';

$message = "";
$message_class = "alert";

$conn->query("CREATE TABLE IF NOT EXISTS password_resets (
    reset_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    token_hash CHAR(64) NOT NULL UNIQUE,
    expires_at DATETIME NOT NULL,
    used_at DATETIME NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
)");

function site_base_url()
{
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $path = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
    return $scheme . '://' . $host . $path;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);

    if ($email == "") {
        $message = "Email is required.";
        $message_class = "alert error";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Please enter a valid email address.";
        $message_class = "alert error";
    } else {
        $stmt = $conn->prepare("SELECT user_id, full_name, email FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $user = $result->fetch_assoc();
            $token = bin2hex(random_bytes(32));
            $token_hash = hash('sha256', $token);
            $expires_at = date('Y-m-d H:i:s', time() + 3600);

            $delete_old = $conn->prepare("DELETE FROM password_resets WHERE user_id = ? OR expires_at < NOW() OR used_at IS NOT NULL");
            $delete_old->bind_param("i", $user['user_id']);
            $delete_old->execute();

            $insert = $conn->prepare("INSERT INTO password_resets (user_id, token_hash, expires_at) VALUES (?, ?, ?)");
            $insert->bind_param("iss", $user['user_id'], $token_hash, $expires_at);
            $insert->execute();

            $reset_link = site_base_url() . "/reset_password.php?token=" . urlencode($token);
            $subject = "Reset your DJ Library password";
            $body = "Hello " . $user['full_name'] . ",\n\n";
            $body .= "Click this link to reset your password:\n" . $reset_link . "\n\n";
            $body .= "This link expires in 1 hour. If you did not request this, please ignore this email.";
            $headers = "From: DJ Library <no-reply@localhost>\r\n";
            $headers .= "Reply-To: no-reply@localhost\r\n";
            $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

            if (mail($user['email'], $subject, $body, $headers)) {
                $message = "A password reset link has been sent to your email.";
            } else {
                $message = "The reset link was created, but the email could not be sent. Please configure SMTP/mail in XAMPP.";
                $message_class = "alert error";
            }
        } else {
            $message = "If that email exists, a password reset link has been sent.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Forgot Password | DJ PHP</title>
  <link rel="stylesheet" href="../css/styles.css?v=auth-split-4">
</head>
<body class="auth-page forgot-page">
  <main class="auth-form-wrap auth-screen">
    <section class="form-panel narrow auth-card">
      <h1>Forgot Password</h1>
      <?php if ($message != ""): ?><p class="<?php echo $message_class; ?>"><?php echo htmlspecialchars($message); ?></p><?php endif; ?>
      <form method="POST" action="forgot_password.php">
        <div class="field-group">
          <label for="email">Email</label>
          <input id="email" name="email" type="email" placeholder="Email" required>
        </div>
        <button class="primary-button" type="submit">SEND LINK</button>
      </form>
    </section>

    <section class="auth-panel">
      <div>
        <h2>Remembered?</h2>
        <p>Go back and sign in with your library account.</p>
        <a class="ghost-button" href="login.php">SIGN IN</a>
      </div>
    </section>
  </main>
</body>
</html>
