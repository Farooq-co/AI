<?php
require_once __DIR__ . '/../../connect.php';
require_once __DIR__ . '/../fee_helpers.php';

$q = sanitizeText($_GET['q'] ?? '');
$page = max(1, sanitizeInt($_GET['page'] ?? 1));
$perPage = 25;
$offset = ($page - 1) * $perPage;
$where = "WHERE 1=1";

if ($q !== '') {
    $escaped = $conn->real_escape_string($q);
    $where .= " AND (s.student_name LIKE '%$escaped%' OR f.opening_balance LIKE '%$escaped%' OR f.closing_balance LIKE '%$escaped%')";
}

$countSql = "SELECT COUNT(*) AS cnt FROM fee_ledgers f LEFT JOIN students s ON f.student_id = s.id " . $where;
$stmt = $conn->prepare($countSql);
if ($stmt === false) {
    die('Prepare failed: ' . $conn->error);
}
$stmt->execute();
$res = $stmt->get_result();
$total = ($row = $res->fetch_assoc()) ? intval($row['cnt']) : 0;
$stmt->close();

$sql = "SELECT f.id, f.student_id, s.student_name, f.opening_balance, f.fee_charges, f.discount_total, f.scholarship_total, f.fine_total, f.payments_total, f.refunds_total, f.closing_balance, f.last_updated FROM fee_ledgers f LEFT JOIN students s ON f.student_id = s.id " . $where . " ORDER BY f.last_updated DESC LIMIT ? OFFSET ?";
$stmt = $conn->prepare($sql);
if ($stmt === false) {
    die('Prepare failed: ' . $conn->error);
}
$stmt->bind_param('ii', $perPage, $offset);
$stmt->execute();
$result = $stmt->get_result();
?>
<div class="container mt-3">
  <h3>Fee Ledger</h3>
  <form class="form-inline mb-2" method="get">
    <input type="text" name="q" class="form-control mr-2" placeholder="Student / amount" value="<?= htmlspecialchars($q) ?>">
    <button class="btn btn-primary">Search</button>
  </form>
  <table class="table table-bordered table-striped">
    <thead>
      <tr>
        <th>#</th>
        <th>Student</th>
        <th>Opening</th>
        <th>Charges</th>
        <th>Discount</th>
        <th>Scholarship</th>
        <th>Fine</th>
        <th>Payments</th>
        <th>Refunds</th>
        <th>Closing</th>
        <th>Last Updated</th>
      </tr>
    </thead>
    <tbody>
      <?php while ($row = $result->fetch_assoc()): ?>
      <tr>
        <td><?= htmlspecialchars($row['id']) ?></td>
        <td><?= htmlspecialchars($row['student_name']) ?></td>
        <td><?= htmlspecialchars(formatMoney($row['opening_balance'])) ?></td>
        <td><?= htmlspecialchars(formatMoney($row['fee_charges'])) ?></td>
        <td><?= htmlspecialchars(formatMoney($row['discount_total'])) ?></td>
        <td><?= htmlspecialchars(formatMoney($row['scholarship_total'])) ?></td>
        <td><?= htmlspecialchars(formatMoney($row['fine_total'])) ?></td>
        <td><?= htmlspecialchars(formatMoney($row['payments_total'])) ?></td>
        <td><?= htmlspecialchars(formatMoney($row['refunds_total'])) ?></td>
        <td><?= htmlspecialchars(formatMoney($row['closing_balance'])) ?></td>
        <td><?= htmlspecialchars($row['last_updated']) ?></td>
      </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
  <?php
  $pages = max(1, ceil($total / $perPage));
  if ($pages > 1) {
      echo '<nav><ul class="pagination">';
      for ($p = 1; $p <= $pages; $p++) {
          $active = $p == $page ? ' active' : '';
          echo '<li class="page-item' . $active . '"><a class="page-link" href="?page=' . $p . '&q=' . urlencode($q) . '">' . $p . '</a></li>';
      }
      echo '</ul></nav>';
  }
  $stmt->close();
  ?>
</div>
