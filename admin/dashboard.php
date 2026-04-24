<?php
$page_title = 'Admin Dashboard';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';
require_admin();
require_once __DIR__ . '/../includes/header.php';

$u = $conn->query("SELECT COUNT(*) c FROM users")->fetch_assoc()['c'];
$e = $conn->query("SELECT COUNT(*) c FROM events")->fetch_assoc()['c'];
$r = $conn->query("SELECT COUNT(*) c FROM registrations")->fetch_assoc()['c'];
$rev = $conn->query("SELECT COALESCE(SUM(amount),0) s FROM invoices WHERE status='Paid'")->fetch_assoc()['s'];
?>
<h1>Admin Dashboard</h1>
<p class="subtitle">Welcome, <?php echo sanitize($_SESSION['admin_user']); ?></p>

<div class="stats">
  <div class="stat"><div class="num"><?php echo $u; ?></div><div class="label">Users</div></div>
  <div class="stat"><div class="num"><?php echo $e; ?></div><div class="label">Events</div></div>
  <div class="stat"><div class="num"><?php echo $r; ?></div><div class="label">Registrations</div></div>
  <div class="stat"><div class="num">₹<?php echo number_format($rev,0); ?></div><div class="label">Revenue</div></div>
</div>

<div class="toolbar">
  <a class="btn btn-primary" href="events.php">Manage Events</a>
  <a class="btn btn-outline" href="users.php">View Users</a>
  <a class="btn btn-outline" href="registrations.php">Registrations</a>
  <a class="btn btn-outline" href="logout.php">Logout</a>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
