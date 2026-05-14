<?php
include 'db_connect.php';
include 'auth.php';

$message = "";
$remembered_email = isset($_COOKIE['remember_email']) ? $_COOKIE['remember_email'] : "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if ($email == "" || $password == "") {
        $message = "Email and password are required.";
    } else {
        $stmt = $conn->prepare("SELECT user_id, full_name, email, password, role FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $user = $result->fetch_assoc();
            $password_ok = password_verify($password, $user['password']);

            if ($password_ok) {
                session_regenerate_id(true);
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['full_name'] = $user['full_name'];
                $_SESSION['role'] = $user['role'];

                if (isset($_POST['remember'])) {
                    setcookie('remember_email', $email, [
                        'expires' => time() + (86400 * 7),
                        'path' => '/',
                        'secure' => false,
                        'httponly' => true,
                        'samesite' => 'Lax'
                    ]);
                } else {
                    setcookie('remember_email', '', [
                        'expires' => time() - 3600,
                        'path' => '/',
                        'secure' => false,
                        'httponly' => true,
                        'samesite' => 'Lax'
                    ]);
                }

                header("Location: dashboard.php");
                exit;
            }
        }

        $message = "Invalid email or password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login | DJ PHP</title>
  <link rel="stylesheet" href="../css/styles.css?v=auth-split-5">
</head>
<body class="auth-page login-page">
  <header class="site-header">
    <a class="brand" href="../index.html"><span class="brand-mark">DJ</span><span>Library</span></a>
    <nav class="nav-links">
      <?php render_nav('login'); ?>
    </nav>
  </header>

  <main class="auth-form-wrap auth-screen">
    <section class="form-panel narrow auth-card">
      <h1>Sign in</h1>
      <?php if ($message != ""): ?><p class="alert error"><?php echo htmlspecialchars($message); ?></p><?php endif; ?>
      <form method="POST" action="login.php">
        <div class="field-group">
          <label for="email">Email</label>
          <input id="email" name="email" type="email" placeholder="Email" value="<?php echo htmlspecialchars($remembered_email); ?>" required>
        </div>
        <div class="field-group">
          <label for="password">Password</label>
          <input id="password" name="password" type="password" placeholder="Password" required>
        </div>
        <label class="check-row remember-row">
            <input type="checkbox" name="remember" value="1" <?php if ($remembered_email != "") echo "checked"; ?>>
            Remember me?
          </label>
        <a class="forgot-link" href="forgot_password.php">Forgot Password?</a>
        <button class="primary-button" type="submit">SIGN IN</button>
      </form>
    </section>

    <section class="auth-panel">
      <div>
        <h2>Hello,!</h2>
        <p>Enter Your Personal Details and start Journey with us</p>
        <a class="ghost-button" href="register.php">SIGN UP</a>
      </div>
    </section>
  </main>
</body>
</html>
