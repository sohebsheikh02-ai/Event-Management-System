<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';
require_login();

$event_id = intval($_POST['event_id'] ?? 0);
$user_id  = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT * FROM events WHERE id=?");
$stmt->bind_param('i',$event_id); $stmt->execute();
$event = $stmt->get_result()->fetch_assoc();
if (!$event) die("Event not found");

// duplicate check
$s = $conn->prepare("SELECT id FROM registrations WHERE user_id=? AND event_id=?");
$s->bind_param('ii',$user_id,$event_id); $s->execute();
if ($s->get_result()->num_rows) {
    header("Location: " . BASE_URL . "/user/dashboard.php"); exit;
}

// insert registration
$stmt = $conn->prepare("INSERT INTO registrations (user_id,event_id) VALUES (?,?)");
$stmt->bind_param('ii',$user_id,$event_id); $stmt->execute();
$reg_id = $stmt->insert_id;

// invoice
$invoice_no = 'INV-' . date('Ymd') . '-' . str_pad($reg_id, 5, '0', STR_PAD_LEFT);
$amount = $event['price'];
$status = $amount > 0 ? 'Paid' : 'Free';
$stmt = $conn->prepare("INSERT INTO invoices (user_id,event_id,registration_id,invoice_number,amount,status) VALUES (?,?,?,?,?,?)");
$stmt->bind_param('iiisds',$user_id,$event_id,$reg_id,$invoice_no,$amount,$status);
$stmt->execute();
$invoice_id = $stmt->insert_id;

header("Location: " . BASE_URL . "/pdf/generate_invoice.php?id=" . $invoice_id);
exit;
