<?php
// books_display.php

session_start();

// 1. Connect to DB
include '../connect.php';

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['user_id']; // Get logged-in user ID

// 2. Fetch Active resources assigned to the user
$sql = "
  SELECT 
    c.id            AS class_id,
    c.name          AS class_name,
    r.id            AS resource_id,
    r.cover_image,
    r.resource_file,
    b.name          AS book_name
  FROM user_resource_assignments ura
  JOIN resources r ON ura.resource_id = r.id AND r.status = 'Active'
  JOIN classes c ON r.classes_id = c.id
  JOIN books b ON r.books_id = b.id
  WHERE ura.user_id = ?
  ORDER BY c.name, b.name
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

// 3. Organize data by class
$classesData = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $classId = $row['class_id'];
        if (!isset($classesData[$classId])) {
            $classesData[$classId] = [
                'class_name' => $row['class_name'],
                'resources'  => []
            ];
        }
        $classesData[$classId]['resources'][] = [
            'resource_id'   => $row['resource_id'],
            'cover_image'   => $row['cover_image'],
            'resource_file' => $row['resource_file'],
            'book_name'     => $row['book_name']
        ];
    }
}
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <title>Books Display</title>
  <?php include '../parts/links3.php'; ?>
  <?php include '../parts/style.php'; ?>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    /* Some extra styling for card layout */
    .book-card {
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.15);
      border-radius: 6px;
      overflow: hidden;
      transition: transform 0.2s;
      background: #fff;
    }
    .book-card:hover {
      transform: scale(1.02);
    }
    .cover-image {
      width: 100%;
      height: 350px;
      object-fit: cover;
      background-color: #eee;
    }
    .book-info {
      padding: 1rem;
    }
  </style>
</head>
<body>
<div class="container-scroller">
  <?php include '../parts/navbar.php'; ?>

  <div class="container-fluid page-body-wrapper">
    <?php include '../parts/setting.php'; ?>
    <?php include '../parts/right_sidebar.php'; ?>
    <?php include '../parts/left_sidebar.php'; ?>

    <div class="main-panel">
      <div class="content-wrapper">
        <h4 class="card-title mb-4">Books Display</h4>
        
        <?php if (!empty($classesData)): ?>
          <ul class="nav nav-pills mb-3" id="classTabs" role="tablist">
            <?php
            $first = true;
            foreach ($classesData as $classId => $classInfo):
              $tabId = "class-$classId";
              $activeClass = $first ? 'active' : '';
            ?>
            <li class="nav-item" role="presentation">
              <button
                class="nav-link <?= $activeClass ?>"
                id="<?= $tabId ?>-tab"
                data-toggle="pill"
                data-target="#<?= $tabId ?>"
                type="button"
                role="tab"
                aria-controls="<?= $tabId ?>"
                aria-selected="<?= $first ? 'true' : 'false' ?>"
              >
                <?= htmlspecialchars($classInfo['class_name'], ENT_QUOTES) ?>
              </button>
            </li>
            <?php
              $first = false;
            endforeach;
            ?>
          </ul>

          <div class="tab-content" id="classTabsContent">
            <?php
            $first = true;
            foreach ($classesData as $classId => $classInfo):
              $tabId = "class-$classId";
              $activeClass = $first ? 'show active' : '';
            ?>
            <div 
              class="tab-pane fade <?= $activeClass ?>" 
              id="<?= $tabId ?>" 
              role="tabpanel" 
              aria-labelledby="<?= $tabId ?>-tab"
            >
              <?php if (!empty($classInfo['resources'])): ?>
                <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4">
                  <?php foreach ($classInfo['resources'] as $res):
                    $coverPath = !empty($res['cover_image'])
                      ? "../uploads/cover_images/" . $res['cover_image']
                      : "https://via.placeholder.com/400x200?text=No+Image";
                    $resourceFile = !empty($res['resource_file'])
                      ? "../uploads/resource_files/" . $res['resource_file']
                      : "";
                    $bookName = htmlspecialchars($res['book_name'], ENT_QUOTES);
                  ?>
                  <div class="col d-flex">
                    <div class="card book-card flex-fill">
                      <img src="<?= $coverPath ?>" alt="Cover Image" class="cover-image">
                      <div class="book-info">
                        <h5 class="card-title"><?= $bookName ?></h5>
                        <?php if ($resourceFile && file_exists($resourceFile)): ?>
                          <a href="<?= $resourceFile ?>" download class="btn btn-outline-primary mt-2">
                            <i class="bi bi-download"></i> Download
                          </a>
                        <?php else: ?>
                          <p class="text-muted mt-2">No File Available</p>
                        <?php endif; ?>
                      </div>
                    </div>
                  </div>
                  <?php endforeach; ?>
                </div>
              <?php else: ?>
                <p class="text-muted">No books available for this class.</p>
              <?php endif; ?>
            </div>
            <?php
              $first = false;
            endforeach;
            ?>
          </div>
        <?php else: ?>
          <p class="text-muted">No classes or books found.</p>
        <?php endif; ?>
      </div>
      <?php include '../parts/footer.php'; ?>
    </div>
  </div>
</div>

<?php include '../parts/links2.php'; ?>
</body>
</html>
