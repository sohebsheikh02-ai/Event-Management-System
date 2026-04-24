<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';

$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $pass  = $_POST['password'] ?? '';
    $stmt  = $conn->prepare("SELECT id,name,password FROM users WHERE email=?");
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $u = $stmt->get_result()->fetch_assoc();
    if ($u && password_verify($pass, $u['password'])) {
        $_SESSION['user_id']   = $u['id'];
        $_SESSION['user_name'] = $u['name'];
        header("Location: " . BASE_URL . "/user/dashboard.php");
        exit;
    } else {
        $err = 'Invalid email or password.';
    }
}

$page_title = 'Login';
require_once __DIR__ . '/includes/header.php';
?>
<div class="form-card">
  <h2>Welcome Back</h2>
  <p class="subtitle">Login to your account.</p>
  <?php if ($err): ?><div class="alert alert-error"><?php echo $err; ?></div><?php endif; ?>
  <form method="post">
    <div class="form-group"><label>Email</label><input type="email" name="email" required></div>
    <div class="form-group"><label>Password</label><input type="password" name="password" required></div>
    <button class="btn btn-primary btn-block">Login</button>
  </form>
  <p style="text-align:center;margin-top:14px">No account? <a href="register.php">Sign up</a></p>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
