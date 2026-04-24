<?php
$page_title = 'Manage Events';
require_once __DIR__ . '/../includes/header.php';
require_admin();

$msg='';
if ($_SERVER['REQUEST_METHOD']==='POST') {
    $action = $_POST['action'] ?? '';
    if ($action==='add' || $action==='edit') {
        $title=$_POST['title']; $desc=$_POST['description']; $d=$_POST['event_date'];
        $t=$_POST['event_time']; $v=$_POST['venue']; $c=$_POST['category']; $p=floatval($_POST['price']);
        if ($action==='add') {
            $s=$conn->prepare("INSERT INTO events (title,description,event_date,event_time,venue,category,price) VALUES (?,?,?,?,?,?,?)");
            $s->bind_param('ssssssd',$title,$desc,$d,$t,$v,$c,$p); $s->execute(); $msg='Event added.';
        } else {
            $id=intval($_POST['id']);
            $s=$conn->prepare("UPDATE events SET title=?,description=?,event_date=?,event_time=?,venue=?,category=?,price=? WHERE id=?");
            $s->bind_param('ssssssdi',$title,$desc,$d,$t,$v,$c,$p,$id); $s->execute(); $msg='Event updated.';
        }
    }
}
if (isset($_GET['del'])) {
    $id=intval($_GET['del']);
    $s=$conn->prepare("DELETE FROM events WHERE id=?"); $s->bind_param('i',$id); $s->execute();
    $msg='Event deleted.';
}

$edit = null;
if (isset($_GET['edit'])) {
    $s=$conn->prepare("SELECT * FROM events WHERE id=?"); $s->bind_param('i',$_GET['edit']); $s->execute();
    $edit=$s->get_result()->fetch_assoc();
}
$events = $conn->query("SELECT * FROM events ORDER BY event_date DESC");
?>
<h1>Manage Events</h1>
<a href="dashboard.php" class="btn btn-outline btn-sm">← Back to Dashboard</a>
<?php if($msg): ?><div class="alert alert-success" style="margin-top:14px"><?php echo $msg; ?></div><?php endif; ?>

<div class="form-card" style="max-width:100%;margin-top:20px">
  <h3><?php echo $edit?'Edit Event':'Add New Event'; ?></h3>
  <form method="post">
    <input type="hidden" name="action" value="<?php echo $edit?'edit':'add'; ?>">
    <?php if($edit): ?><input type="hidden" name="id" value="<?php echo $edit['id']; ?>"><?php endif; ?>
    <div class="grid" style="grid-template-columns:repeat(auto-fit,minmax(220px,1fr))">
      <div class="form-group"><label>Title</label><input name="title" required value="<?php echo $edit?sanitize($edit['title']):''; ?>"></div>
      <div class="form-group"><label>Category</label><input name="category" value="<?php echo $edit?sanitize($edit['category']):''; ?>"></div>
      <div class="form-group"><label>Date</label><input type="date" name="event_date" required value="<?php echo $edit?$edit['event_date']:''; ?>"></div>
      <div class="form-group"><label>Time</label><input type="time" name="event_time" required value="<?php echo $edit?$edit['event_time']:''; ?>"></div>
      <div class="form-group"><label>Venue</label><input name="venue" required value="<?php echo $edit?sanitize($edit['venue']):''; ?>"></div>
      <div class="form-group"><label>Price (₹)</label><input type="number" step="0.01" name="price" value="<?php echo $edit?$edit['price']:'0'; ?>"></div>
    </div>
    <div class="form-group"><label>Description</label><textarea name="description"><?php echo $edit?sanitize($edit['description']):''; ?></textarea></div>
    <button class="btn btn-primary"><?php echo $edit?'Update':'Add'; ?> Event</button>
    <?php if($edit): ?><a href="events.php" class="btn btn-outline">Cancel</a><?php endif; ?>
  </form>
</div>

<h3 style="margin-top:30px">All Events</h3>
<div class="table-wrap">
  <table>
    <thead><tr><th>Title</th><th>Date</th><th>Venue</th><th>Price</th><th>Actions</th></tr></thead>
    <tbody>
      <?php while($e=$events->fetch_assoc()): ?>
      <tr>
        <td><?php echo sanitize($e['title']); ?></td>
        <td><?php echo date('d M Y',strtotime($e['event_date'])); ?></td>
        <td><?php echo sanitize($e['venue']); ?></td>
        <td><?php echo $e['price']==0?'Free':'₹'.number_format($e['price'],2); ?></td>
        <td>
          <a href="?edit=<?php echo $e['id']; ?>" class="btn btn-outline btn-sm">Edit</a>
          <a href="?del=<?php echo $e['id']; ?>" onclick="return confirm('Delete this event?')" class="btn btn-danger btn-sm">Delete</a>
        </td>
      </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
