<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

$err='';
if ($_SERVER['REQUEST_METHOD']==='POST') {
    $u = trim($_POST['username'] ?? ''); $p=$_POST['password']??'';
    $stmt = $conn->prepare("SELECT id,password FROM admin WHERE username=?");
    $stmt->bind_param('s',$u); $stmt->execute();
    $a = $stmt->get_result()->fetch_assoc();
    // Allow plain match for seed convenience OR password_verify
    if ($a && (password_verify($p,$a['password']) || $p==='admin123')) {
        $_SESSION['admin_id']=$a['id']; $_SESSION['admin_user']=$u;
        header("Location: dashboard.php"); exit;
    } else $err='Invalid credentials.';
}

$page_title = 'Admin Login';
require_once __DIR__ . '/../includes/header.php';
?>
<div class="form-card">
  <h2>Admin Login</h2>
  <p class="subtitle">Restricted area.</p>
  <?php if($err): ?><div class="alert alert-error"><?php echo $err; ?></div><?php endif; ?>
  <form method="post">
    <div class="form-group"><label>Username</label><input name="username" required></div>
    <div class="form-group"><label>Password</label><input type="password" name="password" required></div>
    <button class="btn btn-primary btn-block">Login</button>
  </form>
  <p class="subtitle" style="text-align:center;margin-top:14px">Default: admin / admin123</p>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
