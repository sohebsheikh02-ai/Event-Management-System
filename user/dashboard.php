<?php
$page_title = 'My Dashboard';
require_once __DIR__ . '/../includes/header.php';
require_login();

$uid = $_SESSION['user_id'];
$stmt = $conn->prepare("
  SELECT r.id reg_id, r.registered_at, e.title, e.event_date, e.event_time, e.venue, e.price,
         i.id inv_id, i.invoice_number, i.status
  FROM registrations r
  JOIN events e ON e.id = r.event_id
  LEFT JOIN invoices i ON i.registration_id = r.id
  WHERE r.user_id = ?
  ORDER BY r.registered_at DESC");
$stmt->bind_param('i',$uid); $stmt->execute();
$rows = $stmt->get_result();
?>
<h1>My Registered Events</h1>
<p class="subtitle">Welcome back, <?php echo sanitize($_SESSION['user_name']); ?>!</p>

<?php if ($rows->num_rows === 0): ?>
  <div class="alert alert-info">You haven't registered for any events yet. <a href="<?php echo BASE_URL; ?>/index.php">Browse events</a></div>
<?php else: ?>
<div class="table-wrap">
  <table>
    <thead><tr><th>Invoice #</th><th>Event</th><th>Date</th><th>Venue</th><th>Amount</th><th>Status</th><th>Action</th></tr></thead>
    <tbody>
      <?php while ($r = $rows->fetch_assoc()): ?>
      <tr>
        <td><?php echo sanitize($r['invoice_number']); ?></td>
        <td><?php echo sanitize($r['title']); ?></td>
        <td><?php echo date('d M Y', strtotime($r['event_date'])); ?></td>
        <td><?php echo sanitize($r['venue']); ?></td>
        <td><?php echo $r['price']==0?'Free':'₹'.number_format($r['price'],2); ?></td>
        <td><span class="badge <?php echo strtolower($r['status']); ?>"><?php echo sanitize($r['status']); ?></span></td>
        <td>
          <a class="btn btn-primary btn-sm" href="<?php echo BASE_URL; ?>/pdf/generate_invoice.php?id=<?php echo $r['inv_id']; ?>">Preview</a>
          <!-- <a class="btn btn-outline btn-sm" href="<?php echo BASE_URL; ?>/pdf/generate_invoice.php?id=<?php echo $r['inv_id']; ?>&download=html">HTML</a> -->
        </td>
      </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
</div>
<?php endif; ?>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
