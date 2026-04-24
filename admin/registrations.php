<?php
$page_title='Registrations';
require_once __DIR__ . '/../includes/header.php';
require_admin();
$rows=$conn->query("
  SELECT r.id, r.registered_at, u.name user_name, u.email, e.title event_title,
         i.id inv_id, i.invoice_number, i.amount, i.status
  FROM registrations r
  JOIN users u ON u.id=r.user_id
  JOIN events e ON e.id=r.event_id
  LEFT JOIN invoices i ON i.registration_id=r.id
  ORDER BY r.registered_at DESC");
?>
<h1>All Registrations</h1>
<a href="dashboard.php" class="btn btn-outline btn-sm">← Back</a>
<div class="table-wrap" style="margin-top:18px">
  <table>
    <thead><tr><th>Invoice #</th><th>User</th><th>Event</th><th>Amount</th><th>Status</th><th>Date</th><th>Invoice</th></tr></thead>
    <tbody>
      <?php while($r=$rows->fetch_assoc()): ?>
      <tr>
        <td><?php echo sanitize($r['invoice_number']); ?></td>
        <td><?php echo sanitize($r['user_name']); ?><br><small style="color:#6b7280"><?php echo sanitize($r['email']); ?></small></td>
        <td><?php echo sanitize($r['event_title']); ?></td>
        <td>₹<?php echo number_format($r['amount'],2); ?></td>
        <td><span class="badge <?php echo strtolower($r['status']); ?>"><?php echo sanitize($r['status']); ?></span></td>
        <td><?php echo date('d M Y',strtotime($r['registered_at'])); ?></td>
        <td><a class="btn btn-primary btn-sm" href="<?php echo BASE_URL; ?>/pdf/generate_invoice.php?id=<?php echo $r['inv_id']; ?>">Download</a></td>
      </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
