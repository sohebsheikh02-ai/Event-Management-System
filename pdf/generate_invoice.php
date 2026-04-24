<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

$invoice_id = intval($_GET['id'] ?? 0);
$downloadMode = ($_GET['download'] ?? '') === 'html';
$printMode = ($_GET['print'] ?? '') === '1';

$stmt = $conn->prepare("
  SELECT i.*,
         COALESCE(u.name, ru.name) AS user_name,
         COALESCE(u.email, ru.email) AS email,
         COALESCE(u.phone, ru.phone) AS phone,
         COALESCE(e.title, re.title) AS event_title,
         COALESCE(e.event_date, re.event_date) AS event_date,
         COALESCE(e.event_time, re.event_time) AS event_time,
         COALESCE(e.venue, re.venue) AS venue
  FROM invoices i
  LEFT JOIN users u ON u.id = i.user_id
  LEFT JOIN events e ON e.id = i.event_id
  LEFT JOIN registrations r ON r.id = i.registration_id
  LEFT JOIN users ru ON ru.id = r.user_id
  LEFT JOIN events re ON re.id = r.event_id
  WHERE i.id = ?");
$stmt->bind_param('i', $invoice_id);
$stmt->execute();
$inv = $stmt->get_result()->fetch_assoc();

if (!$inv) {
    die('Invoice not found');
}

if (!(is_admin() || (is_logged_in() && $_SESSION['user_id'] == $inv['user_id']))) {
    die('Unauthorized');
}

$invoiceNumber = $inv['invoice_number'] ?: ('INV-' . str_pad((string) $invoice_id, 5, '0', STR_PAD_LEFT));
$invoiceDate = !empty($inv['created_at']) ? date('d M Y', strtotime($inv['created_at'])) : date('d M Y');
$userName = $inv['user_name'] ?: 'Registered User';
$userEmail = $inv['email'] ?: '-';
$userPhone = $inv['phone'] ?: '-';
$eventTitle = $inv['event_title'] ?: 'Event Registration';
$eventDate = !empty($inv['event_date']) ? date('d M Y', strtotime($inv['event_date'])) : '-';
$eventTime = !empty($inv['event_time']) ? date('h:i A', strtotime($inv['event_time'])) : '-';
$venue = $inv['venue'] ?: '-';
$amount = isset($inv['amount']) ? (float) $inv['amount'] : 0.0;
$status = $inv['status'] ?: 'Paid';
$subtotal = $amount;
$tax = round($subtotal * 0.18, 2);
$total = $subtotal + $tax;
$statusClass = strtolower($status);
$baseInvoiceUrl = BASE_URL . '/pdf/generate_invoice.php?id=' . $invoice_id;

function h($value) {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

if ($downloadMode) {
    header('Content-Type: text/html; charset=UTF-8');
    header('Content-Disposition: attachment; filename="invoice_' . preg_replace('/[^A-Za-z0-9_-]/', '_', $invoiceNumber) . '.html"');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Invoice <?php echo h($invoiceNumber); ?></title>
<style>
  :root {
    --ink: #111827;
    --muted: #6b7280;
    --line: #e5e7eb;
    --panel: #ffffff;
    --soft: #f8fafc;
    --brand: #4f46e5;
    --brand-dark: #4338ca;
    --paid-bg: #dcfce7;
    --paid-text: #166534;
    --free-bg: #dbeafe;
    --free-text: #1d4ed8;
    --pending-bg: #fef3c7;
    --pending-text: #92400e;
  }

  * {
    box-sizing: border-box;
  }

  body {
    margin: 0;
    font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
    color: var(--ink);
    background: #eef2ff;
  }

  .receipt-page {
    max-width: 980px;
    margin: 0 auto;
    padding: 32px 20px 60px;
  }

  .receipt-actions {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 12px;
    margin-bottom: 20px;
    flex-wrap: wrap;
  }

  .receipt-actions__title {
    font-size: 15px;
    color: var(--muted);
  }

  .receipt-actions__buttons {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
  }

  .btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-height: 42px;
    padding: 0 16px;
    border-radius: 10px;
    border: 1px solid transparent;
    text-decoration: none;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
  }

  .btn-primary {
    background: var(--brand);
    color: #fff;
  }

  .btn-primary:hover {
    background: var(--brand-dark);
  }

  .btn-secondary {
    background: #fff;
    color: var(--ink);
    border-color: #cbd5e1;
  }

  .receipt {
    background: var(--panel);
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 24px 60px rgba(15, 23, 42, 0.12);
  }

  .receipt-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 20px;
    padding: 34px 40px;
    background: linear-gradient(135deg, var(--brand-dark), #5b4ff1);
    color: #fff;
  }

  .receipt-brand h1 {
    margin: 0 0 8px;
    font-size: 28px;
    font-weight: 700;
    line-height: 1.1;
  }

  .receipt-brand p,
  .receipt-title p {
    margin: 0;
    opacity: 0.9;
  }

  .receipt-title {
    text-align: right;
  }

  .receipt-title h2 {
    margin: 0 0 8px;
    font-size: 32px;
    letter-spacing: 0.04em;
  }

  .receipt-body {
    padding: 36px 40px 40px;
  }

  .receipt-meta,
  .receipt-panels {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 20px;
  }

  .receipt-meta {
    margin-bottom: 26px;
  }

  .meta-card,
  .panel {
    background: var(--soft);
    border: 1px solid var(--line);
    border-radius: 16px;
    padding: 18px 20px;
  }

  .label {
    margin: 0 0 8px;
    font-size: 12px;
    font-weight: 700;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    color: var(--muted);
  }

  .value {
    margin: 0;
    font-size: 24px;
    font-weight: 700;
  }

  .panel h3 {
    margin: 0 0 14px;
    font-size: 15px;
  }

  .panel p {
    margin: 0 0 8px;
    line-height: 1.55;
    word-break: break-word;
  }

  .receipt-panels {
    margin-bottom: 28px;
  }

  .line-items {
    width: 100%;
    border-collapse: collapse;
    overflow: hidden;
    border-radius: 16px;
    margin-bottom: 28px;
  }

  .line-items thead th {
    padding: 16px 18px;
    background: var(--brand);
    color: #fff;
    font-size: 13px;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    text-align: left;
  }

  .line-items thead th:nth-child(2) {
    width: 90px;
    text-align: center;
  }

  .line-items thead th:nth-child(3) {
    width: 180px;
    text-align: right;
  }

  .line-items tbody td {
    padding: 18px;
    border: 1px solid var(--line);
    vertical-align: top;
    background: #fff;
  }

  .line-items tbody td:nth-child(2) {
    text-align: center;
  }

  .line-items tbody td:nth-child(3) {
    text-align: right;
    font-weight: 600;
  }

  .receipt-summary {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 24px;
    margin-bottom: 30px;
  }

  .status-pill {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 120px;
    padding: 12px 18px;
    border-radius: 999px;
    font-size: 13px;
    font-weight: 700;
    letter-spacing: 0.06em;
    text-transform: uppercase;
  }

  .status-pill.paid {
    background: var(--paid-bg);
    color: var(--paid-text);
  }

  .status-pill.free {
    background: var(--free-bg);
    color: var(--free-text);
  }

  .status-pill.pending {
    background: var(--pending-bg);
    color: var(--pending-text);
  }

  .totals-card {
    width: 320px;
    max-width: 100%;
    margin-left: auto;
    border: 1px solid var(--line);
    border-radius: 16px;
    overflow: hidden;
    background: #fff;
  }

  .totals-row {
    display: flex;
    justify-content: space-between;
    gap: 16px;
    padding: 14px 18px;
    border-bottom: 1px solid var(--line);
  }

  .totals-row:last-child {
    border-bottom: 0;
  }

  .totals-row strong {
    font-size: 20px;
  }

  .receipt-footer {
    padding-top: 20px;
    border-top: 1px solid var(--line);
    color: var(--muted);
    line-height: 1.7;
  }

  @page {
    size: A4;
    margin: 14mm;
  }

  @media (max-width: 760px) {
    .receipt-page {
      padding: 16px 12px 32px;
    }

    .receipt-header,
    .receipt-body {
      padding: 24px 20px;
    }

    .receipt-header,
    .receipt-summary {
      flex-direction: column;
    }

    .receipt-title {
      text-align: left;
    }

    .receipt-meta,
    .receipt-panels {
      grid-template-columns: 1fr;
    }

    .line-items {
      display: block;
      overflow-x: auto;
    }
  }

  @media print {
    body {
      background: #fff;
    }

    .receipt-page {
      max-width: none;
      padding: 0;
    }

    .receipt-actions {
      display: none;
    }

    .receipt {
      box-shadow: none;
      border-radius: 0;
    }
  }
</style>
</head>
<body<?php echo $printMode ? ' onload="window.print()"' : ''; ?>>
  <div class="receipt-page">
    <div class="receipt-actions">
      <div class="receipt-actions__title">Receipt preview for <?php echo h($invoiceNumber); ?></div>
      <div class="receipt-actions__buttons">
        <!-- <a class="btn btn-secondary" href="<?php echo h($baseInvoiceUrl . '&download=html'); ?>">Download HTML</a> -->
        <!-- <a class="btn btn-secondary" href="<?php echo h($baseInvoiceUrl . '&print=1'); ?>" target="_blank" rel="noopener">Open Print View</a> -->
        <button class="btn btn-primary" type="button" onclick="window.print()">Print / Save PDF</button>
      </div>
    </div>

    <article class="receipt">
      <header class="receipt-header">
        <div class="receipt-brand">
          <h1>EventHub</h1>
          <p>Event Management System</p>
        </div>
        <div class="receipt-title">
          <h2>INVOICE</h2>
          <p>Generated on <?php echo h($invoiceDate); ?></p>
        </div>
      </header>

      <div class="receipt-body">
        <section class="receipt-meta">
          <div class="meta-card">
            <p class="label">Invoice Number</p>
            <p class="value"><?php echo h($invoiceNumber); ?></p>
          </div>
          <div class="meta-card">
            <p class="label">Invoice Date</p>
            <p class="value"><?php echo h($invoiceDate); ?></p>
          </div>
        </section>

        <section class="receipt-panels">
          <div class="panel">
            <h3>Bill To</h3>
            <p><?php echo h($userName); ?></p>
            <p><?php echo h($userEmail); ?></p>
            <p><?php echo h($userPhone); ?></p>
          </div>
          <div class="panel">
            <h3>Event Details</h3>
            <p><?php echo h($eventTitle); ?></p>
            <p><?php echo h($eventDate . ' at ' . $eventTime); ?></p>
            <p><?php echo h($venue); ?></p>
          </div>
        </section>

        <table class="line-items">
          <thead>
            <tr>
              <th>Description</th>
              <th>Qty</th>
              <th>Amount</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>Event Registration: <?php echo h($eventTitle); ?></td>
              <td>1</td>
              <td>INR <?php echo h(number_format($amount, 2)); ?></td>
            </tr>
          </tbody>
        </table>

        <section class="receipt-summary">
          <div class="status-pill <?php echo h($statusClass); ?>"><?php echo h($status); ?></div>
          <div class="totals-card">
            <div class="totals-row">
              <span>Subtotal</span>
              <span>INR <?php echo h(number_format($subtotal, 2)); ?></span>
            </div>
            <div class="totals-row">
              <span>GST (18%)</span>
              <span>INR <?php echo h(number_format($tax, 2)); ?></span>
            </div>
            <div class="totals-row">
              <strong>Total</strong>
              <strong>INR <?php echo h(number_format($total, 2)); ?></strong>
            </div>
          </div>
        </section>

        <footer class="receipt-footer">
          <div>Thank you for registering with EventHub.</div>
          <div>This is a computer-generated invoice. For queries contact support@eventhub.com.</div>
        </footer>
      </div>
    </article>
  </div>
</body>
</html>
