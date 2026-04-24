<?php
$page_title = 'Event Details';
require_once __DIR__ . '/includes/header.php';

$id = intval($_GET['id'] ?? 0);
$stmt = $conn->prepare("SELECT * FROM events WHERE id=?");
$stmt->bind_param('i',$id); $stmt->execute();
$e = $stmt->get_result()->fetch_assoc();
if (!$e) { echo '<div class="alert alert-error">Event not found.</div>'; require __DIR__.'/includes/footer.php'; exit; }

$registered = false;
if (is_logged_in()) {
    $s = $conn->prepare("SELECT id FROM registrations WHERE user_id=? AND event_id=?");
    $s->bind_param('ii',$_SESSION['user_id'],$id); $s->execute();
    $registered = $s->get_result()->num_rows > 0;
}
$past = strtotime($e['event_date']) < strtotime(date('Y-m-d'));
?>
<div class="event-detail">
  <span class="badge <?php echo $e['price']==0?'free':''; ?>"><?php echo $e['price']==0?'FREE':'₹'.number_format($e['price'],2); ?></span>
  <h1 style="margin-top:10px"><?php echo sanitize($e['title']); ?></h1>
  <p class="subtitle"><?php echo sanitize($e['category']); ?></p>

  <div class="info">
    <div class="info-item"><div class="l">Date</div><div class="v"><?php echo date('d M Y', strtotime($e['event_date'])); ?></div></div>
    <div class="info-item"><div class="l">Time</div><div class="v"><?php echo date('h:i A', strtotime($e['event_time'])); ?></div></div>
    <div class="info-item"><div class="l">Venue</div><div class="v"><?php echo sanitize($e['venue']); ?></div></div>
    <div class="info-item"><div class="l">Price</div><div class="v"><?php echo $e['price']==0?'Free':'₹'.number_format($e['price'],2); ?></div></div>
  </div>

  <h3>About this event</h3>
  <p style="margin:10px 0 24px;color:#374151"><?php echo nl2br(sanitize($e['description'])); ?></p>

  <?php if ($past): ?>
    <div class="alert alert-info">This event has already taken place.</div>
  <?php elseif (!is_logged_in()): ?>
    <a href="login.php" class="btn btn-primary">Login to Register</a>
  <?php elseif ($registered): ?>
    <div class="alert alert-success">✅ You're already registered. <a href="user/dashboard.php">View your tickets</a></div>
  <?php else: ?>
    <form method="post" action="register_event.php">
      <input type="hidden" name="event_id" value="<?php echo $e['id']; ?>">
      <button class="btn btn-success">Confirm Registration</button>
    </form>
  <?php endif; ?>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
