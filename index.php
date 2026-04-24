<?php
$page_title = 'Browse Events';
require_once __DIR__ . '/includes/header.php';

$search = $_GET['q'] ?? '';
$category = $_GET['cat'] ?? '';

$sql = "SELECT * FROM events WHERE 1=1";
$params = []; $types = '';
if ($search !== '') { $sql .= " AND (title LIKE ? OR description LIKE ?)"; $s="%$search%"; $params[]=$s; $params[]=$s; $types.='ss'; }
if ($category !== '') { $sql .= " AND category = ?"; $params[]=$category; $types.='s'; }
$sql .= " ORDER BY event_date ASC";

$stmt = $conn->prepare($sql);
if ($params) $stmt->bind_param($types, ...$params);
$stmt->execute();
$events = $stmt->get_result();

$cats = $conn->query("SELECT DISTINCT category FROM events WHERE category IS NOT NULL AND category!=''");
?>
<section class="hero">
  <div class="hero-copy">
    <h1>Discover Amazing Events</h1>
    <p>Browse, register and get instant invoices for events near you.</p>
  </div>
  <div class="hero-search">
    <form class="toolbar hero-toolbar" method="get">
      <input type="text" name="q" placeholder="Search events..." value="<?php echo sanitize($search); ?>">
      <select name="cat">
        <option value="">All categories</option>
        <?php while ($c = $cats->fetch_assoc()): ?>
          <option value="<?php echo sanitize($c['category']); ?>" <?php echo $category===$c['category']?'selected':''; ?>><?php echo sanitize($c['category']); ?></option>
        <?php endwhile; ?>
      </select>
      <button class="btn btn-primary" type="submit">Search events</button>
    </form>
  </div>
</section>

<div class="grid">
  <?php if ($events->num_rows === 0): ?>
    <p class="subtitle">No events found.</p>
  <?php else: while ($e = $events->fetch_assoc()):
    $past = strtotime($e['event_date']) < strtotime(date('Y-m-d')); ?>
    <article class="card">
      <div class="card-body">
        <span class="badge <?php echo $e['price']==0?'free':''; ?>"><?php echo $e['price']==0?'FREE':'₹'.number_format($e['price'],2); ?></span>
        <h3 style="margin-top:10px"><?php echo sanitize($e['title']); ?></h3>
        <div class="meta">📅 <?php echo date('d M Y', strtotime($e['event_date'])); ?> · ⏰ <?php echo date('h:i A', strtotime($e['event_time'])); ?></div>
        <div class="meta">📍 <?php echo sanitize($e['venue']); ?></div>
        <p class="desc"><?php echo sanitize(substr($e['description'],0,110)); ?>...</p>
        <a href="event.php?id=<?php echo $e['id']; ?>" class="btn btn-primary btn-block"><?php echo $past?'View Details':'Register Now'; ?></a>
      </div>
    </article>
  <?php endwhile; endif; ?>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
