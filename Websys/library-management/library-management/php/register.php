<?php
include 'db_connect.php';
include 'auth.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $role = $_POST['role'];

    if ($full_name == "" || $email == "" || $password == "" || $confirm_password == "") {
        $message = "Please complete all required fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Please enter a valid email address.";
    } elseif ($password !== $confirm_password) {
        $message = "Passwords do not match.";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO users (full_name, email, password, role) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $full_name, $email, $hashed_password, $role);

        if ($stmt->execute()) {
            $message = "Account created successfully. You may now log in.";
        } else {
            $message = "Registration failed. The email may already be used.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register | DJ PHP</title>
  <link rel="stylesheet" href="../css/styles.css?v=auth-split-5">
</head>
<body class="auth-page register-page">
  <header class="site-header">
    <a class="brand" href="../index.html"><span class="brand-mark">DJ</span><span>Library</span></a>
    <nav class="nav-links">
      <?php render_nav('register'); ?>
    </nav>
  </header>

  <main class="auth-form-wrap auth-screen">
    <section class="auth-panel">
      <div>
        <h2>Welcome Back</h2>
        <p>To keep connected with us please login with your personal info</p>
        <a class="ghost-button" href="login.php">SIGN IN</a>
      </div>
    </section>

    <section class="form-panel narrow auth-card">
      <h1>Create Account</h1>
      <?php if ($message != ""): ?><p class="alert"><?php echo htmlspecialchars($message); ?></p><?php endif; ?>
      <form method="POST" action="register.php">
        <div class="field-group">
          <label for="full_name">Full Name</label>
          <input id="full_name" name="full_name" type="text" placeholder="Name" required>
        </div>
        <div class="field-group">
          <label for="email">Email</label>
          <input id="email" name="email" type="email" placeholder="Email" required>
        </div>
        <div class="field-group password-field">
          <label for="password">Password</label>
          <input id="password" name="password" type="password" placeholder="Password" required>
          <button class="password-toggle" type="button" aria-label="Show password" data-password-toggle="password" data-visible="false">
            <span class="password-toggle-icon" aria-hidden="true"></span>
          </button>
        </div>
        <div class="field-group password-field">
          <label for="confirm_password">Confirm Password</label>
          <input id="confirm_password" name="confirm_password" type="password" placeholder="Confirm Password" required>
          <button class="password-toggle" type="button" aria-label="Show confirm password" data-password-toggle="confirm_password" data-visible="false">
            <span class="password-toggle-icon" aria-hidden="true"></span>
          </button>
        </div>
        <div class="field-group role-group">
          <label for="role">Role</label>
          <select id="role" name="role">
            <option>Librarian</option>
            <option>Library Assistant</option>
            <option>Administrator</option>
          </select>
        </div>
        <button class="primary-button" type="submit">SIGN UP</button>
      </form>
    </section>
  </main>
  <script>
    document.querySelectorAll('[data-password-toggle]').forEach(function (button) {
      button.addEventListener('click', function () {
        var input = document.getElementById(button.dataset.passwordToggle);
        var showPassword = input.type === 'password';

        input.type = showPassword ? 'text' : 'password';
        button.dataset.visible = showPassword ? 'true' : 'false';
        button.setAttribute('aria-label', showPassword ? 'Hide password' : 'Show password');
      });
    });
  </script>
</body>
</html>
