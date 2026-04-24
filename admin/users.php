<?php
$page_title='Users';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';
require_admin();
require_once __DIR__ . '/../includes/header.php';
$users=$conn->query("SELECT id,name,email,phone,created_at FROM users ORDER BY created_at DESC");
?>
<h1>Registered Users</h1>
<a href="dashboard.php" class="btn btn-outline btn-sm">← Back</a>
<div class="table-wrap" style="margin-top:18px">
  <table>
    <thead><tr><th>ID</th><th>Name</th><th>Email</th><th>Phone</th><th>Joined</th></tr></thead>
    <tbody>
      <?php while($u=$users->fetch_assoc()): ?>
      <tr>
        <td><?php echo $u['id']; ?></td>
        <td><?php echo sanitize($u['name']); ?></td>
        <td><?php echo sanitize($u['email']); ?></td>
        <td><?php echo sanitize($u['phone']?:'-'); ?></td>
        <td><?php echo date('d M Y',strtotime($u['created_at'])); ?></td>
      </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
