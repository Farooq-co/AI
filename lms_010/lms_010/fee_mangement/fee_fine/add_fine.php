<?php
include_once __DIR__ . '/../../connect.php';
include_once __DIR__ . '/../fee_helpers.php';
// Minimal add fine form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  header('Location: save_fine.php');
  exit;
}
// fetch students
$students = [];
$res = $conn->query("SELECT id, student_name FROM students ORDER BY student_name");
while($r = $res->fetch_assoc()) $students[] = $r;
?>
<div class="container">
  <h3>Add Fee Fine</h3>
  <form method="post" action="save_fine.php">
    <div class="mb-3">
      <label>Student</label>
      <select name="student_id" class="form-control" required>
        <option value="">-- Select --</option>
        <?php foreach($students as $s): ?>
          <option value="<?php echo $s['id']; ?>"><?php echo htmlspecialchars($s['student_name']); ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="mb-3">
      <label>Invoice ID (optional)</label>
      <input name="invoice_id" class="form-control" />
    </div>
    <div class="mb-3">
      <label>Fine Type</label>
      <select name="fine_type" class="form-control">
        <option>Fixed Fine</option>
        <option>Per Day Fine</option>
        <option>Percentage Fine</option>
      </select>
    </div>
    <div class="mb-3">
      <label>Rate</label>
      <input name="rate" class="form-control" required />
    </div>
    <div class="mb-3">
      <label>Days Overdue</label>
      <input name="days_overdue" class="form-control" />
    </div>
    <div class="mb-3">
      <label>Due Date</label>
      <input type="date" name="due_date" class="form-control" required />
    </div>
    <button class="btn btn-primary">Save Fine</button>
  </form>
</div>
