<?php
$page_title = 'Browse Events';
require_once __DIR__ . '/includes/header.php';

$search   = $_GET['q']    ?? '';
$category = $_GET['cat']  ?? '';
$current_page = max(1, (int)($_GET['page'] ?? 1));
$per_page     = 10;
$offset       = ($current_page - 1) * $per_page;

// --- Count total matching events for pagination ---
$count_sql = "SELECT COUNT(*) AS total FROM events WHERE 1=1";
$params = []; $types = '';
if ($search !== '')   { $count_sql .= " AND (title LIKE ? OR description LIKE ?)"; $s = "%$search%"; $params[] = $s; $params[] = $s; $types .= 'ss'; }
if ($category !== '') { $count_sql .= " AND category = ?"; $params[] = $category; $types .= 's'; }

$count_stmt = $conn->prepare($count_sql);
if ($params) $count_stmt->bind_param($types, ...$params);
$count_stmt->execute();
$total_count = $count_stmt->get_result()->fetch_assoc()['total'];
$total_pages = max(1, (int)ceil($total_count / $per_page));
$current_page = min($current_page, $total_pages);

// --- Fetch paginated events ---
$sql = "SELECT * FROM events WHERE 1=1";
$params = []; $types = '';
if ($search !== '')   { $sql .= " AND (title LIKE ? OR description LIKE ?)"; $s = "%$search%"; $params[] = $s; $params[] = $s; $types .= 'ss'; }
if ($category !== '') { $sql .= " AND category = ?"; $params[] = $category; $types .= 's'; }
$sql .= " ORDER BY event_date ASC LIMIT ? OFFSET ?";
$params[] = $per_page; $params[] = $offset; $types .= 'ii';

$stmt = $conn->prepare($sql);
if ($params) $stmt->bind_param($types, ...$params);
$stmt->execute();
$events = $stmt->get_result();

$cats = $conn->query("SELECT DISTINCT category FROM events WHERE category IS NOT NULL AND category!=''");

// Build base query string for pagination links (preserves search & category)
$query_base = http_build_query(array_filter(['q' => $search, 'cat' => $category]));
$query_base = $query_base ? $query_base . '&' : '';
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

<?php if ($total_pages > 1): ?>
<nav class="pagination" aria-label="Event pages">
  <?php if ($current_page > 1): ?>
    <a class="page-btn" href="?<?php echo $query_base; ?>page=<?php echo $current_page - 1; ?>">&#8592; Previous</a>
  <?php else: ?>
    <span class="page-btn disabled">&#8592; Previous</span>
  <?php endif; ?>

  <div class="page-numbers">
    <?php
      $start_page = max(1, $current_page - 2);
      $end_page   = min($total_pages, $current_page + 2);
      if ($start_page > 1): ?>
        <a class="page-num" href="?<?php echo $query_base; ?>page=1">1</a>
        <?php if ($start_page > 2): ?><span class="page-ellipsis">&hellip;</span><?php endif; ?>
      <?php endif;
      for ($p = $start_page; $p <= $end_page; $p++): ?>
        <?php if ($p === $current_page): ?>
          <span class="page-num active"><?php echo $p; ?></span>
        <?php else: ?>
          <a class="page-num" href="?<?php echo $query_base; ?>page=<?php echo $p; ?>"><?php echo $p; ?></a>
        <?php endif; ?>
      <?php endfor;
      if ($end_page < $total_pages): ?>
        <?php if ($end_page < $total_pages - 1): ?><span class="page-ellipsis">&hellip;</span><?php endif; ?>
        <a class="page-num" href="?<?php echo $query_base; ?>page=<?php echo $total_pages; ?>"><?php echo $total_pages; ?></a>
      <?php endif; ?>
  </div>

  <span class="page-info">Page <?php echo $current_page; ?> of <?php echo $total_pages; ?></span>

  <?php if ($current_page < $total_pages): ?>
    <a class="page-btn" href="?<?php echo $query_base; ?>page=<?php echo $current_page + 1; ?>">Next &#8594;</a>
  <?php else: ?>
    <span class="page-btn disabled">Next &#8594;</span>
  <?php endif; ?>
</nav>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
