<?php
require_once __DIR__ . '/../../connect.php';
require_once __DIR__ . '/../fee_helpers.php';

// Basic list with pagination, search, and filters
$q = sanitizeText($_GET['q'] ?? '');
$invoice_id = sanitizeInt($_GET['invoice_id'] ?? 0);
$page = max(1, sanitizeInt($_GET['page'] ?? 1));
$perPage = 25;
$offset = ($page - 1) * $perPage;

$where = "WHERE COALESCE(deleted_at, '') = ''";
$params = [];
$types = '';
if ($q !== '') {
    $where .= " AND (installment_no LIKE ? OR amount LIKE ? OR paid_amount LIKE ?)";
    $like = "%$q%";
    $params[] = $like; $params[] = $like; $params[] = $like;
    $types .= 'sss';
}
if ($invoice_id > 0) {
    $where .= " AND invoice_id = ?";
    $params[] = $invoice_id;
    $types .= 'i';
}

$countSql = "SELECT COUNT(*) as cnt FROM fee_installments " . $where;
$stmt = $conn->prepare($countSql);
if ($stmt === false) { die('Prepare failed: ' . $conn->error); }
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$res = $stmt->get_result();
$total = ($row = $res->fetch_assoc()) ? intval($row['cnt']) : 0;
$stmt->close();

$sql = "SELECT id, invoice_id, installment_no, due_date, amount, paid_amount, remaining_amount, status, created_at FROM fee_installments " . $where . " ORDER BY due_date ASC LIMIT ? OFFSET ?";
$stmt = $conn->prepare($sql);
if ($stmt === false) { die('Prepare failed: ' . $conn->error); }
// bind params + limit/offset (preserve previous types)
$params_list = $params;
$types_list = $types;
$params_list[] = $perPage;
$params_list[] = $offset;
$types_list .= 'ii';
$stmt->bind_param($types_list, ...$params_list);
$stmt->execute();
$result = $stmt->get_result();

// Simple HTML table output (Bootstrap-compatible)
?><div class="container mt-3">
    <h3>Installments</h3>
    <form class="form-inline mb-2" method="get">
        <input type="text" name="q" class="form-control mr-2" placeholder="Search" value="<?= htmlspecialchars($q) ?>">
        <input type="number" name="invoice_id" class="form-control mr-2" placeholder="Invoice ID" value="<?= htmlspecialchars($invoice_id) ?>">
        <button class="btn btn-primary">Search</button>
    </form>
    <table class="table table-striped table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th>Invoice</th>
                <th>Installment No</th>
                <th>Due Date</th>
                <th>Amount</th>
                <th>Paid</th>
                <th>Remaining</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['id']) ?></td>
                <td><?= htmlspecialchars($row['invoice_id']) ?></td>
                <td><?= htmlspecialchars($row['installment_no']) ?></td>
                <td><?= htmlspecialchars($row['due_date']) ?></td>
                <td><?= htmlspecialchars(formatMoney($row['amount'])) ?></td>
                <td><?= htmlspecialchars(formatMoney($row['paid_amount'])) ?></td>
                <td><?= htmlspecialchars(formatMoney($row['remaining_amount'])) ?></td>
                <td><?= htmlspecialchars($row['status']) ?></td>
                <td>
                    <a href="edit_installment.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-secondary">Edit</a>
                    <a href="delete_installment.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete?')">Delete</a>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
    <?php
    // simple pager
    $pages = max(1, ceil($total / $perPage));
    if ($pages > 1) {
        echo '<nav><ul class="pagination">';
        for ($p = 1; $p <= $pages; $p++) {
            $active = $p == $page ? ' active' : '';
            echo '<li class="page-item' . $active . '"><a class="page-link" href="?page=' . $p . '&q=' . urlencode($q) . '&invoice_id=' . $invoice_id . '">' . $p . '</a></li>';
        }
        echo '</ul></nav>';
    }
    $stmt->close();
    ?>
</div>

<?php
