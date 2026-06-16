<?php
include_once __DIR__ . '/../../connect.php';
include_once __DIR__ . '/../fee_helpers.php';
// Simple list view for fee fines
$sql = "SELECT ff.*, s.student_name FROM fee_fines ff LEFT JOIN students s ON ff.student_id=s.id ORDER BY ff.created_at DESC";
$result = $conn->query($sql);
?>
<div class="container">
  <h3>Fee Fines</h3>
  <a class="btn btn-primary mb-2" href="add_fine.php">Add Fine</a>
  <table class="table table-striped">
    <thead>
      <tr>
        <th>ID</th>
        <th>Student</th>
        <th>Invoice ID</th>
        <th>Type</th>
        <th>Rate</th>
        <th>Days Overdue</th>
        <th>Amount</th>
        <th>Due Date</th>
        <th>Status</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
<?php while($row = mysqli_fetch_assoc($result)): ?>
      <tr>
        <td><?php echo htmlspecialchars($row['id']); ?></td>
        <td><?php echo htmlspecialchars($row['student_name'] ?? '—'); ?></td>
        <td><?php echo htmlspecialchars($row['invoice_id']); ?></td>
        <td><?php echo htmlspecialchars($row['fine_type']); ?></td>
        <td><?php echo htmlspecialchars($row['rate']); ?></td>
        <td><?php echo htmlspecialchars($row['days_overdue']); ?></td>
        <td><?php echo htmlspecialchars($row['calculated_amount']); ?></td>
        <td><?php echo htmlspecialchars($row['due_date']); ?></td>
        <td><?php echo htmlspecialchars($row['status']); ?></td>
        <td>
          <a class="btn btn-sm btn-danger" href="delete_fine.php?id=<?php echo $row['id']; ?>" onclick="return confirm('Delete fine?');">Delete</a>
        </td>
      </tr>
<?php endwhile; ?>
    </tbody>
  </table>
</div>
