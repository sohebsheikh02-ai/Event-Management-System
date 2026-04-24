<?php
$page_title = 'Sign Up';
require_once __DIR__ . '/includes/header.php';

$err = $ok = '';
if ($_SERVER['REQUEST_METHOD']==='POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $pass  = $_POST['password'] ?? '';
    if (!$name || !$email || !$pass) $err='All required fields must be filled.';
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) $err='Invalid email.';
    elseif (strlen($pass) < 6) $err='Password must be at least 6 characters.';
    else {
        $stmt = $conn->prepare("SELECT id FROM users WHERE email=?");
        $stmt->bind_param('s',$email); $stmt->execute();
        if ($stmt->get_result()->num_rows) $err='Email already registered.';
        else {
            $hash = password_hash($pass, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (name,email,password,phone) VALUES (?,?,?,?)");
            $stmt->bind_param('ssss',$name,$email,$hash,$phone);
            if ($stmt->execute()) { $ok='Account created! Please login.'; }
            else $err='Registration failed.';
        }
    }
}
?>
<div class="form-card">
  <h2>Create Account</h2>
  <p class="subtitle">Join EventHub to register for events.</p>
  <?php if($err): ?><div class="alert alert-error"><?php echo $err; ?></div><?php endif; ?>
  <?php if($ok): ?><div class="alert alert-success"><?php echo $ok; ?> <a href="login.php">Login</a></div><?php endif; ?>
  <form method="post">
    <div class="form-group"><label>Full Name *</label><input name="name" required></div>
    <div class="form-group"><label>Email *</label><input type="email" name="email" required></div>
    <div class="form-group"><label>Phone</label><input name="phone"></div>
    <div class="form-group"><label>Password * (min 6 chars)</label><input type="password" name="password" required></div>
    <button class="btn btn-primary btn-block">Sign Up</button>
  </form>
  <p style="text-align:center;margin-top:14px">Already have an account? <a href="login.php">Login</a></p>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
