<?php
require_once __DIR__ . '/config.php';
session_start();

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $password = $_POST['password'] ?? '';
  if (hash_equals($adminPassword, $password)) {
    $_SESSION['authed'] = true;
    header('Location: index.php');
    exit;
  }
  $error = 'Invalid password';
}
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Admin Login</title>
    <link rel="stylesheet" href="styles.css" />
  </head>
  <body>
    <div class="bg">
      <div class="orb orb-1"></div>
      <div class="orb orb-2"></div>
      <div class="orb orb-3"></div>
      <div class="grid"></div>
    </div>
    <main class="shell">
      <section class="panel auth-panel">
        <h1>Admin Login</h1>
        <p class="sub">Enter the admin password to manage signatures.</p>
        <?php if ($error): ?>
          <p class="auth-error"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>
        <form method="post" class="form">
          <label>
            Password
            <input type="password" name="password" required />
          </label>
          <button type="submit">Sign In</button>
        </form>
      </section>
    </main>
  </body>
</html>
