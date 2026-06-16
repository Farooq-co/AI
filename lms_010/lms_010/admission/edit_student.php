<!DOCTYPE html>
<html lang="en">
<head>
    <title>Edit Student Information</title>
    <?php include '../parts/links1.php'; ?>
    <?php include '../parts/style.php'; ?>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        /* ========== MODERN STYLES ========== */
        * { box-sizing: border-box; }
        .form-section { background: #f8f9fa; border-radius: 8px; padding: 20px; margin-bottom: 20px; border-left: 4px solid #4e73df; }
        .section-title { color: #2c3e50; font-weight: 700; margin-bottom: 20px; font-size: 1.2rem; border-bottom: 2px solid #4e73df; padding-bottom: 8px; display: inline-block; }
        .required-field::after { content: " *"; color: red; font-weight: bold; }
        .field-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 5px; }
        .field-header label { margin-bottom: 0; font-weight: 700; color: #2c3e50; font-size: 0.85rem; }
        .add-btn-mini { background: #4e73df; border: none; color: white; font-size: 11px; padding: 4px 12px; border-radius: 15px; cursor: pointer; transition: all 0.2s ease; display: inline-flex; align-items: center; gap: 4px; }
        .add-btn-mini:hover { background: #2e59d9; transform: translateY(-1px); }
        .image-preview-container { border: 1px dashed #dee2e6; padding: 10px; border-radius: 5px; text-align: center; background: #f8f9fa; }
        .image-preview-container img { max-width: 100%; max-height: 150px; border-radius: 5px; }
        .action-buttons { position: sticky; bottom: 0; background: white; padding: 15px; border-top: 1px solid #dee2e6; margin-top: 20px; z-index: 100; border-radius: 8px; box-shadow: 0 -2px 10px rgba(0,0,0,0.05); }
        #loading-bar-spinner { position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); z-index: 9999; }
        .form-control, .form-select { display: block; width: 100% !important; min-width: 100% !important; padding: 0.5rem 0.75rem; font-size: 0.9rem; font-weight: 500; line-height: 1.5; color: #1f2937 !important; background-color: #ffffff; border: 1px solid #d1d5db; border-radius: 6px; height: auto; }
        select.form-select { appearance: none; -webkit-appearance: none; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%234e73df' stroke-width='2'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right 0.75rem center; padding-right: 2rem; }
        .form-control:focus, .form-select:focus { border-color: #4e73df; box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25); }
        .card { border-radius: 10px; box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,0.075); margin-bottom: 20px; border: none; }
        .card-header { background: linear-gradient(135deg, #4e73df 0%, #224abe 100%); color: white; border-radius: 10px 10px 0 0 !important; padding: 12px 20px; }
        .card-header h6 { margin: 0; font-weight: 600; }
        .card-body { padding: 20px; background: #ffffff; }
        .form-group { margin-bottom: 15px; }
        .btn-primary { background: #4e73df; border-color: #4e73df; padding: 8px 20px; font-weight: 600; }
        .btn-primary:hover { background: #2e59d9; }
        .border-warning-custom { border: 1px solid #ffc107; border-radius: 6px; padding: 8px 10px; background: #fff8e7; }
        .row { margin-left: 0; margin-right: 0; }
        .col-md-3, .col-md-4, .col-md-6, .col-sm-12 { padding-left: 10px; padding-right: 10px; }
        @media (max-width: 768px) { .btn { margin-bottom: 5px; width: 100%; } .card-body { padding: 15px; } }
        label { font-weight: 700; color: #2c3e50; margin-bottom: 5px; font-size: 0.85rem; display: block; }
        .text-muted { font-size: 0.75rem; margin-top: 4px; display: block; }
        .guardian-search-section { background: linear-gradient(135deg, #f0f4ff 0%, #e8edfc 100%); border-radius: 12px; padding: 20px; margin-bottom: 20px; border: 1px solid #c5d5f5; }
        .guardian-info-card { background: white; border-radius: 12px; margin-top: 20px; border: 1px solid #d1e0ff; overflow: hidden; }
        .guardian-info-card .card-header-primary { background: linear-gradient(135deg, #4e73df 0%, #224abe 100%); color: white; padding: 12px 20px; font-weight: 700; }
        .guardian-info-card .card-body-info { padding: 20px; }
        .guardian-col-display { background: #f8fafc; border-radius: 10px; padding: 15px; height: 100%; border: 1px solid #e2e8f0; }
        .guardian-col-display h6 { color: #4e73df; font-weight: 700; border-bottom: 2px solid #4e73df; padding-bottom: 8px; margin-bottom: 15px; }
        .guardian-detail-row { display: flex; flex-wrap: wrap; margin-bottom: 10px; font-size: 0.85rem; }
        .guardian-detail-label { font-weight: 700; width: 130px; color: #2c3e50; }
        .guardian-detail-value { flex: 1; color: #1f2937; }
        .btn-search-guardian { background: #4e73df; border-color: #4e73df; color: white; padding: 8px 24px; font-weight: 600; border-radius: 6px; }
        .btn-add-guardian-new { background: #1cc88a; border-color: #1cc88a; color: white; padding: 8px 20px; font-weight: 600; border-radius: 6px; }
        .btn-clear-guardian { background: #4b49ac; border-color: #4b49ac; color: white; padding: 8px 20px; font-weight: 600; border-radius: 6px; }
        .guardian-action-row { display: flex; flex-wrap: wrap; align-items: center; gap: 12px; }
        .guardian-search-input-wrapper { flex: 2; min-width: 250px; }
        .guardian-buttons-wrapper { display: flex; gap: 10px; flex-wrap: wrap; }
        @media (max-width: 768px) { .guardian-action-row { flex-direction: column; align-items: stretch; } }
        
        /* Guardian Search Results Styles */
        .guardian-search-results {
            background: white;
            border-radius: 10px;
            border: 1px solid #d1e0ff;
            max-height: 450px;
            overflow-y: auto;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .guardian-result-item {
            padding: 15px;
            border-bottom: 1px solid #eef2f7;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .guardian-result-item:hover {
            background: #f0f4ff;
            transform: translateX(3px);
        }
        .guardian-result-item:last-child {
            border-bottom: none;
        }
        .guardian-result-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }
        .guardian-result-type {
            background: #4e73df;
            color: white;
            padding: 2px 10px;
            border-radius: 12px;
            font-size: 0.7rem;
            font-weight: 600;
        }
        .guardian-result-name {
            font-weight: 700;
            color: #2c3e50;
            font-size: 1rem;
        }
        .guardian-result-name i {
            color: #4e73df;
            margin-right: 5px;
        }
        .guardian-result-details {
            font-size: 0.8rem;
            color: #6c757d;
            margin-top: 8px;
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
        }
        .guardian-result-details span {
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }
        .guardian-result-details i {
            color: #4e73df;
        }
        .select-guardian-badge {
            background: #1cc88a;
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        .guardian-type-badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 0.65rem;
            font-weight: 600;
            margin-left: 8px;
        }
        .guardian-type-father { background: #4e73df; color: white; }
        .guardian-type-mother { background: #e74a3b; color: white; }
        .guardian-type-guardian { background: #1cc88a; color: white; }
    </style>
</head>
<body>
<?php
$currentDate = date('Y-m-d');

// Get student ID from URL
$studentId = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($studentId <= 0) {
    echo "<script>window.location.href='student_list.php';</script>";
    exit;
}

// Fetch student data
include '../connect.php';

$studentQuery = "SELECT s.*, 
                    c.name as class_name,
                    sec.name as section_name,
                    g.name as group_name,
                    cat.name as category_name,
                    rel.name as religion_name,
                    gen.name as gender_name,
                    bg.name as blood_group_name
                 FROM students s 
                 LEFT JOIN classes c ON s.class_id = c.id 
                 LEFT JOIN sections sec ON s.section_id = sec.id
                 LEFT JOIN `groups` g ON s.group_id = g.id
                 LEFT JOIN student_category cat ON s.student_category_id = cat.id
                 LEFT JOIN religion rel ON s.religion_id = rel.id
                 LEFT JOIN gender gen ON s.gender_id = gen.id
                 LEFT JOIN blood_group bg ON s.blood_group_id = bg.id
                 WHERE s.id = $studentId";

$studentResult = $conn->query($studentQuery);

if (!$studentResult || $studentResult->num_rows == 0) {
    echo "<script>window.location.href='student_list.php';</script>";
    exit;
}

$student = $studentResult->fetch_assoc();

// Fetch guardian data if exists
$guardianData = null;
if ($student['guardian_id'] && $student['guardian_id'] > 0) {
    $guardianQuery = "SELECT * FROM student_guardians WHERE id = " . $student['guardian_id'];
    $guardianResult = $conn->query($guardianQuery);
    if ($guardianResult && $guardianResult->num_rows > 0) {
        $guardianData = $guardianResult->fetch_assoc();
        
        // Get city and area names
        if ($guardianData['present_city_id']) {
            $cityQuery = "SELECT name FROM cities WHERE id = " . intval($guardianData['present_city_id']);
            $cityResult = $conn->query($cityQuery);
            if ($cityResult && $cityResult->num_rows > 0) {
                $city = $cityResult->fetch_assoc();
                $guardianData['present_city_name'] = $city['name'];
            }
        }
        
        if ($guardianData['permanent_city_id']) {
            $cityQuery = "SELECT name FROM cities WHERE id = " . intval($guardianData['permanent_city_id']);
            $cityResult = $conn->query($cityQuery);
            if ($cityResult && $cityResult->num_rows > 0) {
                $city = $cityResult->fetch_assoc();
                $guardianData['permanent_city_name'] = $city['name'];
            }
        }
        
        if ($guardianData['present_area_id']) {
            $areaQuery = "SELECT name FROM areas WHERE id = " . intval($guardianData['present_area_id']);
            $areaResult = $conn->query($areaQuery);
            if ($areaResult && $areaResult->num_rows > 0) {
                $area = $areaResult->fetch_assoc();
                $guardianData['present_area_name'] = $area['name'];
            }
        }
        
        if ($guardianData['permanent_area_id']) {
            $areaQuery = "SELECT name FROM areas WHERE id = " . intval($guardianData['permanent_area_id']);
            $areaResult = $conn->query($areaQuery);
            if ($areaResult && $areaResult->num_rows > 0) {
                $area = $areaResult->fetch_assoc();
                $guardianData['permanent_area_name'] = $area['name'];
            }
        }
    }
}
?>
<div class="container-scroller">
    <?php include '../parts/navbar.php'; ?>
    <div class="container-fluid page-body-wrapper">
        <?php include '../parts/setting.php'; ?>
        <?php include '../parts/right_sidebar.php'; ?>
        <?php include '../parts/left_sidebar.php'; ?>
        <div class="main-panel">
            <div class="content-wrapper" style="padding: 15px;">
                <div class="container-fluid">
                    <form id="admissionForm" enctype="multipart/form-data" autocomplete="off">
                        <input type="hidden" id="txtStudentID" name="txtStudentID" value="<?php echo $studentId; ?>">
                        <input type="hidden" id="txtGuardianID" name="txtGuardianID" value="<?php echo $student['guardian_id'] ?? 0; ?>">
                        <input type="hidden" id="existingPicture" name="existing_picture" value="<?php echo htmlspecialchars($student['student_picture'] ?? ''); ?>">

                        <!-- Action Buttons -->
                        <div class="d-flex justify-content-end mb-3" style="gap: 5px;">
                            <a href="student_list.php" class="btn btn-info px-3 py-2">
                                <i class="ti-arrow-left"></i> Go Back
                            </a>
                            <div id="UpdateButton" class="text-end">
                                <button type="button" class="btn btn-primary" onclick="updateStudentInfo();"><i class="bi bi-save me-1"></i> Update Student</button>
                            </div>
                            <a href="add_student.php" class="btn btn-clear-guardian px-3 py-2">
                                <i class="bi bi-plus-circle me-1"></i> Add New Student
                            </a>
                            <button type="reset" class="btn btn-secondary px-3 py-2" onclick="resetForm();">
                                <i class="ti-reload"></i> Reset
                            </button>
                        </div>

                        <!-- Basic Info Section -->
                        <div class="card">
                            <div class="card-header">
                                <h6 class="card-title m-0" style="color: white !important;">
                                    <i class="bi bi-pencil-square me-2" style="color: white !important;"></i>
                                    Edit Student Information
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3 col-sm-12">
                                        <div class="form-group">
                                            <div class="field-header">
                                                <label class="required-field">CLASS</label>
                                                <button type="button" class="add-btn-mini" onclick="window.open('../setting_lms/class.php', '_blank');">
                                                    <i class="bi bi-plus"></i> Add Class
                                                </button>
                                            </div>
                                            <select id="ddlSchoolClasses" name="ddlSchoolClasses" class="form-select" required>
                                                <option value="0">Select Class</option>
                                                <?php
                                                $classQuery = "SELECT id, name FROM classes WHERE status = 'Active' ORDER BY name";
                                                $classResult = $conn->query($classQuery);
                                                while ($class = $classResult->fetch_assoc()) {
                                                    $selected = ($class['id'] == $student['class_id']) ? 'selected' : '';
                                                    echo '<option value="' . $class['id'] . '" ' . $selected . '>' . htmlspecialchars($class['name']) . '</option>';
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-sm-12">
                                        <div class="form-group">
                                            <div class="field-header">
                                                <label class="required-field">SECTION</label>
                                                <button type="button" class="add-btn-mini" onclick="window.open('../setting_lms/section.php', '_blank');">
                                                    <i class="bi bi-plus"></i> Add Section
                                                </button>
                                            </div>
                                            <select id="ddlSchoolClassSection" name="ddlSchoolClassSection" class="form-select" required>
                                                <option value="0">Select Section</option>
                                                <?php
                                                $sectionQuery = "SELECT id, name FROM sections WHERE status = 'Active' ORDER BY name";
                                                $sectionResult = $conn->query($sectionQuery);
                                                while ($section = $sectionResult->fetch_assoc()) {
                                                    $selected = ($section['id'] == $student['section_id']) ? 'selected' : '';
                                                    echo '<option value="' . $section['id'] . '" ' . $selected . '>' . htmlspecialchars($section['name']) . '</option>';
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-sm-12">
                                        <div class="form-group">
                                            <div class="field-header">
                                                <label class="required-field">GROUP</label>
                                                <button type="button" class="add-btn-mini" onclick="window.open('../setting_lms/group.php', '_blank');">
                                                    <i class="bi bi-plus"></i> Add Group
                                                </button>
                                            </div>
                                            <select id="ddlGroups" name="ddlGroups" class="form-select">
                                                <option value="0">Select Group</option>
                                                <?php
                                                $groupQuery = "SELECT id, name FROM `groups` WHERE status = 'Active' ORDER BY name";
                                                $groupResult = $conn->query($groupQuery);
                                                while ($group = $groupResult->fetch_assoc()) {
                                                    $selected = ($group['id'] == $student['group_id']) ? 'selected' : '';
                                                    echo '<option value="' . $group['id'] . '" ' . $selected . '>' . htmlspecialchars($group['name']) . '</option>';
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-sm-12">
                                        <div class="form-group">
                                            <label for="txtAdmissionDate" class="required-field">ADMISSION DATE</label>
                                            <input type="date" class="form-control" id="txtAdmissionDate" name="txtAdmissionDate" value="<?php echo $student['admission_date']; ?>" required>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-sm-12">
                                        <div class="form-group">
                                            <label>ADMISSION NUMBER</label>
                                            <input type="text" id="txtAdmissionNumber" name="txtAdmissionNumber" class="form-control" placeholder="Admission Number" value="<?php echo htmlspecialchars($student['admission_number'] ?? ''); ?>" autocomplete="off">
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-sm-12">
                                        <div class="form-group">
                                            <label class="required-field">STUDENT NAME</label>
                                            <input type="text" id="txtStudentName" name="txtStudentName" class="form-control" placeholder="Student Name" value="<?php echo htmlspecialchars($student['student_name']); ?>" required autocomplete="off">
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-sm-12">
                                        <div class="form-group">
                                            <label class="required-field">FATHER NAME</label>
                                            <input type="text" id="txtFatherName" name="txtFatherName" class="form-control" placeholder="Father Name" value="<?php echo htmlspecialchars($student['father_name']); ?>" required autocomplete="off">
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-sm-12">
                                        <div class="form-group">
                                            <label for="txtDateOfBirth" class="required-field">DATE OF BIRTH</label>
                                            <input type="date" class="form-control" id="txtDateOfBirth" name="txtDateOfBirth" value="<?php echo $student['date_of_birth']; ?>" required>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-sm-12">
                                        <div class="form-group">
                                            <label>ROLL NUMBER</label>
                                            <input type="text" id="txtRollNumber" name="txtRollNumber" class="form-control" placeholder="Roll Number" value="<?php echo htmlspecialchars($student['roll_number'] ?? ''); ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-sm-12">
                                        <div class="form-group">
                                            <div class="field-header">
                                                <label class="required-field">STUDENT CATEGORY</label>
                                                <button type="button" class="add-btn-mini" onclick="window.open('../setting_lms/student_category.php', '_blank');"><i class="bi bi-plus"></i> Add</button>
                                            </div>
                                            <select id="ddlStudentCategoryIdFk" name="ddlStudentCategoryIdFk" class="form-select">
                                                <option value="0">Select Category</option>
                                                <?php
                                                $categoryQuery = "SELECT id, name FROM student_category WHERE status = 'Active' ORDER BY name";
                                                $categoryResult = $conn->query($categoryQuery);
                                                while ($category = $categoryResult->fetch_assoc()) {
                                                    $selected = ($category['id'] == $student['student_category_id']) ? 'selected' : '';
                                                    echo '<option value="' . $category['id'] . '" ' . $selected . '>' . htmlspecialchars($category['name']) . '</option>';
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-sm-12">
                                        <div class="form-group">
                                            <div class="field-header">
                                                <label class="required-field">RELIGION</label>
                                                <button type="button" class="add-btn-mini" onclick="window.open('../setting_lms/religion.php', '_blank');"><i class="bi bi-plus"></i> Add</button>
                                            </div>
                                            <select id="ddlReligionIdFk" name="ddlReligionIdFk" class="form-select">
                                                <option value="0">Select Religion</option>
                                                <?php
                                                $religionQuery = "SELECT id, name FROM religion WHERE status = 'Active' ORDER BY name";
                                                $religionResult = $conn->query($religionQuery);
                                                while ($religion = $religionResult->fetch_assoc()) {
                                                    $selected = ($religion['id'] == $student['religion_id']) ? 'selected' : '';
                                                    echo '<option value="' . $religion['id'] . '" ' . $selected . '>' . htmlspecialchars($religion['name']) . '</option>';
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-sm-12">
                                        <div class="form-group">
                                            <div class="field-header">
                                                <label class="required-field">GENDER</label>
                                                <button type="button" class="add-btn-mini" onclick="window.open('../setting_lms/gender.php', '_blank');"><i class="bi bi-plus"></i> Add</button>
                                            </div>
                                            <select id="ddlGenderIdFk" name="ddlGenderIdFk" class="form-select">
                                                <option value="0">Select Gender</option>
                                                <?php
                                                $genderQuery = "SELECT id, name FROM gender WHERE status = 'Active' ORDER BY name";
                                                $genderResult = $conn->query($genderQuery);
                                                while ($gender = $genderResult->fetch_assoc()) {
                                                    $selected = ($gender['id'] == $student['gender_id']) ? 'selected' : '';
                                                    echo '<option value="' . $gender['id'] . '" ' . $selected . '>' . htmlspecialchars($gender['name']) . '</option>';
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-sm-12">
                                        <div class="form-group">
                                            <div class="field-header">
                                                <label>BLOOD GROUP</label>
                                                <button type="button" class="add-btn-mini" onclick="window.open('../setting_lms/blood_group.php', '_blank');"><i class="bi bi-plus"></i> Add</button>
                                            </div>
                                            <select id="ddlBloodGroupIdFk" name="ddlBloodGroupIdFk" class="form-select">
                                                <option value="0">NA</option>
                                                <?php
                                                $bloodQuery = "SELECT id, name FROM blood_group WHERE status = 'Active' ORDER BY name";
                                                $bloodResult = $conn->query($bloodQuery);
                                                while ($blood = $bloodResult->fetch_assoc()) {
                                                    $selected = ($blood['id'] == $student['blood_group_id']) ? 'selected' : '';
                                                    echo '<option value="' . $blood['id'] . '" ' . $selected . '>' . htmlspecialchars($blood['name']) . '</option>';
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-sm-12">
                                        <div class="form-group">
                                            <label for="txtAdmissionEffectiveDate" class="required-field">ADMISSION EFFECTIVE DATE</label>
                                            <input type="date" class="form-control" id="txtAdmissionEffectiveDate" name="txtAdmissionEffectiveDate" value="<?php echo $student['admission_effective_date']; ?>" required>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-sm-12">
                                        <div class="form-group">
                                            <label>FAMILY NUMBER</label>
                                            <input type="text" id="txtFamilyNumber" name="txtFamilyNumber" class="form-control" placeholder="Family Number" value="<?php echo htmlspecialchars($student['family_number'] ?? ''); ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-sm-12">
                                        <div class="form-group">
                                            <label>HOBBIES</label>
                                            <textarea id="txtHobbies" name="txtHobbies" class="form-control" rows="2" placeholder="Hobbies"><?php echo htmlspecialchars($student['hobbies'] ?? ''); ?></textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-sm-12">
                                        <div class="form-group">
                                            <label>PLACE OF BIRTH</label>
                                            <textarea id="txtplaceofbirth" name="txtplaceofbirth" class="form-control" rows="2" placeholder="Place of Birth"><?php echo htmlspecialchars($student['place_of_birth'] ?? ''); ?></textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-sm-12">
                                        <div class="form-group">
                                            <div class="field-header">
                                                <label>FEE PACKAGE</label>
                                                <button type="button" class="add-btn-mini" onclick="window.open('../setting_lms/fee_package.php', '_blank');"><i class="bi bi-plus"></i> Add</button>
                                            </div>
                                            <select id="ddlFeePackage" name="ddlFeePackage" class="form-select">
                                                <option value="0">Select Fee Package</option>
                                                <?php
                                                $feeQuery = "SELECT id, name FROM fee_packages WHERE status = 'Active' ORDER BY name";
                                                $feeResult = $conn->query($feeQuery);
                                                while ($fee = $feeResult->fetch_assoc()) {
                                                    $selected = ($fee['id'] == $student['fee_package_id']) ? 'selected' : '';
                                                    echo '<option value="' . $fee['id'] . '" ' . $selected . '>' . htmlspecialchars($fee['name']) . '</option>';
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-sm-12">
                                        <div class="form-group">
                                            <div class="field-header">
                                                <label>STUDENT PICTURE</label>
                                                <button type="button" class="add-btn-mini" style="background:#e74a3b;" onclick="removeStudentPicture();"><i class="bi bi-trash"></i> Remove</button>
                                            </div>
                                            <input type="file" id="StudentPicture" name="StudentPicture" class="form-control" accept="image/jpg,image/png,image/jpeg" onchange="validateImageSize(this)">
                                            <small class="text-muted">Max size: 2MB. Allowed: JPG, PNG, JPEG</small>
                                            <p id="ImageSize" class="text-danger" style="display:none;"></p>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-sm-12">
                                        <div id="currentImagePreview" class="image-preview-container" style="<?php echo (!empty($student['student_picture']) ? 'display:block;' : 'display:none;'); ?>">
                                            <label>Image Preview</label>
                                            <?php if (!empty($student['student_picture']) && file_exists('../uploads/students/' . $student['student_picture'])): ?>
                                                <img id="studentImagePreview" src="../uploads/students/<?php echo htmlspecialchars($student['student_picture']); ?>" alt="Preview">
                                            <?php else: ?>
                                                <img id="studentImagePreview" src="" alt="Preview">
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-sm-12">
                                        <div class="border-warning-custom">
                                            <label>STUDENT SEARCH 001</label>
                                            <input type="text" class="form-control" id="StudentSearch1" name="StudentSearch1" maxlength="15" placeholder="XXXXX-XXXXXXX-X" value="<?php echo htmlspecialchars($student['student_search1'] ?? ''); ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-sm-12">
                                        <div class="border-warning-custom">
                                            <label>STUDENT SEARCH 002</label>
                                            <input type="text" class="form-control" id="StudentSearch2" name="StudentSearch2" value="<?php echo htmlspecialchars($student['student_search2'] ?? ''); ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-sm-12">
                                        <div class="border-warning-custom">
                                            <label>STUDENT SEARCH 003</label>
                                            <input type="text" class="form-control" id="StudentSearch3" name="StudentSearch3" value="<?php echo htmlspecialchars($student['student_search3'] ?? ''); ?>">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- GUARDIAN SEARCH SECTION -->
                        <div class="guardian-search-section">
                            <div class="guardian-action-row">
                                <div class="guardian-search-input-wrapper">
                                    <input type="text" id="guardianSearchInput" class="form-control" placeholder="Search by Phone Number, WhatsApp Number, CNIC or Name..." autocomplete="off" style="border-color: #4e73df;">
                                </div>
                                <div class="guardian-buttons-wrapper">
                                    <button type="button" id="btnSearchGuardian" class="btn btn-search-guardian">
                                        <i class="bi bi-search"></i> Search Guardian
                                    </button>
                                    <button type="button" class="btn btn-clear-guardian" onclick="clearGuardianInfo();">
                                        <i class="bi bi-eraser"></i> Clear Guardian
                                    </button>
                                    <button type="button" class="btn btn-add-guardian-new" onclick="window.open('add_guardian.php', '_blank');">
                                        <i class="bi bi-plus-circle"></i> Add New Guardian
                                    </button>
                                </div>
                            </div>
                            <!-- Search Results List -->
                            <div id="guardianSearchResults" style="display: none; margin-top: 15px;"></div>
                            <!-- Selected Guardian Info Display -->
                            <div id="guardianInfoDisplay" style="<?php echo ($guardianData) ? 'display:block;' : 'display:none;'; ?>">
                                <?php if ($guardianData): ?>
                                <div class="guardian-info-card">
                                    <div class="card-header-primary"><i class="bi bi-shield-lock"></i> Guardian Information 
                                        <button type="button" class="btn btn-sm btn-light float-end" onclick="clearGuardianInfo();" style="padding: 2px 10px;"><i class="bi bi-x"></i> Remove</button>
                                    </div>
                                    <div class="card-body-info"><div class="row">
                                        
                                        <?php if (!empty($guardianData['father_name'])): ?>
                                        <div class="col-md-4"><div class="guardian-col-display"><h6><i class="bi bi-person-badge"></i> Father's Information</h6>
                                            <div class="guardian-detail-row"><div class="guardian-detail-label">Full Name:</div><div class="guardian-detail-value"><?php echo htmlspecialchars($guardianData['father_name']); ?></div></div>
                                            <div class="guardian-detail-row"><div class="guardian-detail-label">CNIC:</div><div class="guardian-detail-value"><?php echo htmlspecialchars($guardianData['father_cnic'] ?? '-'); ?></div></div>
                                            <div class="guardian-detail-row"><div class="guardian-detail-label">Mobile Number:</div><div class="guardian-detail-value"><?php echo htmlspecialchars($guardianData['father_mobile'] ?? '-'); ?></div></div>
                                            <div class="guardian-detail-row"><div class="guardian-detail-label">WhatsApp Number:</div><div class="guardian-detail-value"><?php echo htmlspecialchars($guardianData['father_whatsapp_number'] ?? '-'); ?></div></div>
                                            <div class="guardian-detail-row"><div class="guardian-detail-label">Email Address:</div><div class="guardian-detail-value"><?php echo htmlspecialchars($guardianData['father_email'] ?? '-'); ?></div></div>
                                            <div class="guardian-detail-row"><div class="guardian-detail-label">Profession:</div><div class="guardian-detail-value"><?php echo htmlspecialchars($guardianData['father_profession'] ?? '-'); ?></div></div>
                                            <div class="guardian-detail-row"><div class="guardian-detail-label">Education:</div><div class="guardian-detail-value"><?php echo htmlspecialchars($guardianData['father_education'] ?? '-'); ?></div></div>
                                        </div></div>
                                        <?php endif; ?>
                                        
                                        <?php if (!empty($guardianData['mother_name'])): ?>
                                        <div class="col-md-4"><div class="guardian-col-display"><h6><i class="bi bi-person"></i> Mother's Information</h6>
                                            <div class="guardian-detail-row"><div class="guardian-detail-label">Full Name:</div><div class="guardian-detail-value"><?php echo htmlspecialchars($guardianData['mother_name']); ?></div></div>
                                            <div class="guardian-detail-row"><div class="guardian-detail-label">CNIC:</div><div class="guardian-detail-value"><?php echo htmlspecialchars($guardianData['mother_cnic'] ?? '-'); ?></div></div>
                                            <div class="guardian-detail-row"><div class="guardian-detail-label">Mobile Number:</div><div class="guardian-detail-value"><?php echo htmlspecialchars($guardianData['mother_mobile'] ?? '-'); ?></div></div>
                                            <div class="guardian-detail-row"><div class="guardian-detail-label">WhatsApp Number:</div><div class="guardian-detail-value"><?php echo htmlspecialchars($guardianData['mother_whatsapp_number'] ?? '-'); ?></div></div>
                                            <div class="guardian-detail-row"><div class="guardian-detail-label">Email Address:</div><div class="guardian-detail-value"><?php echo htmlspecialchars($guardianData['mother_email'] ?? '-'); ?></div></div>
                                            <div class="guardian-detail-row"><div class="guardian-detail-label">Profession:</div><div class="guardian-detail-value"><?php echo htmlspecialchars($guardianData['mother_profession'] ?? '-'); ?></div></div>
                                            <div class="guardian-detail-row"><div class="guardian-detail-label">Education:</div><div class="guardian-detail-value"><?php echo htmlspecialchars($guardianData['mother_education'] ?? '-'); ?></div></div>
                                        </div></div>
                                        <?php endif; ?>
                                        
                                        <?php if (!empty($guardianData['guardian_name'])): ?>
                                        <div class="col-md-4"><div class="guardian-col-display"><h6><i class="bi bi-shield"></i> Guardian's Information</h6>
                                            <div class="guardian-detail-row"><div class="guardian-detail-label">Full Name:</div><div class="guardian-detail-value"><?php echo htmlspecialchars($guardianData['guardian_name']); ?></div></div>
                                            <div class="guardian-detail-row"><div class="guardian-detail-label">CNIC:</div><div class="guardian-detail-value"><?php echo htmlspecialchars($guardianData['guardian_cnic'] ?? '-'); ?></div></div>
                                            <div class="guardian-detail-row"><div class="guardian-detail-label">Mobile Number:</div><div class="guardian-detail-value"><?php echo htmlspecialchars($guardianData['guardian_mobile'] ?? '-'); ?></div></div>
                                            <div class="guardian-detail-row"><div class="guardian-detail-label">WhatsApp Number:</div><div class="guardian-detail-value"><?php echo htmlspecialchars($guardianData['guardian_whatsapp_number'] ?? '-'); ?></div></div>
                                            <div class="guardian-detail-row"><div class="guardian-detail-label">Email Address:</div><div class="guardian-detail-value"><?php echo htmlspecialchars($guardianData['guardian_email'] ?? '-'); ?></div></div>
                                            <div class="guardian-detail-row"><div class="guardian-detail-label">Profession:</div><div class="guardian-detail-value"><?php echo htmlspecialchars($guardianData['guardian_profession'] ?? '-'); ?></div></div>
                                            <div class="guardian-detail-row"><div class="guardian-detail-label">Education:</div><div class="guardian-detail-value"><?php echo htmlspecialchars($guardianData['guardian_education'] ?? '-'); ?></div></div>
                                        </div></div>
                                        <?php endif; ?>
                                        
                                        <div class="col-12 mt-3"><div class="guardian-col-display"><h6><i class="bi bi-geo-alt"></i> Address Information</h6>
                                            <div class="row"><div class="col-md-6"><div class="guardian-detail-row"><div class="guardian-detail-label">Present Address:</div><div class="guardian-detail-value"><?php echo htmlspecialchars($guardianData['present_address'] ?? '-'); ?></div></div>
                                            <div class="guardian-detail-row"><div class="guardian-detail-label">City:</div><div class="guardian-detail-value"><?php echo htmlspecialchars($guardianData['present_city_name'] ?? $guardianData['present_city_id'] ?? '-'); ?></div></div>
                                            <div class="guardian-detail-row"><div class="guardian-detail-label">Area:</div><div class="guardian-detail-value"><?php echo htmlspecialchars($guardianData['present_area_name'] ?? '-'); ?></div></div>
                                            <div class="guardian-detail-row"><div class="guardian-detail-label">Province:</div><div class="guardian-detail-value"><?php echo htmlspecialchars($guardianData['present_province'] ?? '-'); ?></div></div>
                                            <div class="guardian-detail-row"><div class="guardian-detail-label">Country:</div><div class="guardian-detail-value"><?php echo htmlspecialchars($guardianData['present_country'] ?? '-'); ?></div></div></div>
                                            <div class="col-md-6"><div class="guardian-detail-row"><div class="guardian-detail-label">Permanent Address:</div><div class="guardian-detail-value"><?php echo htmlspecialchars($guardianData['permanent_address'] ?? '-'); ?></div></div>
                                            <div class="guardian-detail-row"><div class="guardian-detail-label">City:</div><div class="guardian-detail-value"><?php echo htmlspecialchars($guardianData['permanent_city_name'] ?? $guardianData['permanent_city_id'] ?? '-'); ?></div></div>
                                            <div class="guardian-detail-row"><div class="guardian-detail-label">Area:</div><div class="guardian-detail-value"><?php echo htmlspecialchars($guardianData['permanent_area_name'] ?? '-'); ?></div></div>
                                            <div class="guardian-detail-row"><div class="guardian-detail-label">Province:</div><div class="guardian-detail-value"><?php echo htmlspecialchars($guardianData['permanent_province'] ?? '-'); ?></div></div>
                                            <div class="guardian-detail-row"><div class="guardian-detail-label">Country:</div><div class="guardian-detail-value"><?php echo htmlspecialchars($guardianData['permanent_country'] ?? '-'); ?></div></div></div></div>
                                        </div></div>
                                        
                                    </div></div>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
            <?php include '../parts/footer.php'; ?>
        </div>
    </div>
</div>

<div class="load" id="loading-bar-spinner" style="display: none;">
    <div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>
</div>

<?php include '../parts/links2.php'; ?>
<?php $conn->close(); ?>

<script>
    document.querySelectorAll('input, select, textarea').forEach(function(el) { el.setAttribute('autocomplete', 'off'); });
    function getCurrentDate() { return new Date().toISOString().slice(0,10); }
    
    $(document).ready(function() {
        $('#btnSearchGuardian').on('click', function() { searchGuardian(); });
        $('#guardianSearchInput').on('keypress', function(e) { if(e.which === 13) searchGuardian(); });
    });
    
    function searchGuardian() {
        var searchTerm = $('#guardianSearchInput').val().trim();
        if(searchTerm === '') { 
            Swal.fire('Warning', 'Please enter Phone Number, WhatsApp Number, CNIC or Name to search', 'warning'); 
            return; 
        }
        $('#loading-bar-spinner').show();
        $.ajax({
            url: 'ajax/search_guardian.php',
            type: 'POST',
            data: { search: searchTerm },
            dataType: 'json',
            success: function(response) {
                $('#loading-bar-spinner').hide();
                if(response.success && response.guardians && response.guardians.length > 0) {
                    displayGuardianResults(response.guardians);
                } else {
                    Swal.fire('Not Found', response.message || 'No guardian found', 'info');
                    $('#guardianSearchResults').hide();
                    $('#guardianInfoDisplay').hide();
                    $('#txtGuardianID').val('0');
                }
            },
            error: function() { 
                $('#loading-bar-spinner').hide(); 
                Swal.fire('Error', 'Search failed', 'error'); 
            }
        });
    }
    
    function displayGuardianResults(guardians) {
        var html = '<div class="guardian-search-results">';
        html += '<div class="card-header-primary" style="padding: 10px 15px;"><i class="bi bi-list-ul"></i> Select Guardian (' + guardians.length + ' found)</div>';
        html += '<div>';
        
        for(var i = 0; i < guardians.length; i++) {
            var g = guardians[i];
            
            // Create separate entries for Father, Mother, and Guardian if they exist
            var entries = [];
            
            // Father entry
            if(g.father_name && g.father_name.trim() !== '') {
                entries.push({
                    type: 'Father',
                    typeClass: 'guardian-type-father',
                    icon: 'bi bi-person-badge',
                    name: g.father_name,
                    phone: g.father_mobile,
                    whatsapp: g.father_whatsapp_number,
                    cnic: g.father_cnic,
                    email: g.father_email,
                    profession: g.father_profession,
                    id: g.id,
                    relation: 'father'
                });
            }
            
            // Mother entry
            if(g.mother_name && g.mother_name.trim() !== '') {
                entries.push({
                    type: 'Mother',
                    typeClass: 'guardian-type-mother',
                    icon: 'bi bi-person',
                    name: g.mother_name,
                    phone: g.mother_mobile,
                    whatsapp: g.mother_whatsapp_number,
                    cnic: g.mother_cnic,
                    email: g.mother_email,
                    profession: g.mother_profession,
                    id: g.id,
                    relation: 'mother'
                });
            }
            
            // Guardian entry
            if(g.guardian_name && g.guardian_name.trim() !== '') {
                entries.push({
                    type: 'Guardian',
                    typeClass: 'guardian-type-guardian',
                    icon: 'bi bi-shield',
                    name: g.guardian_name,
                    phone: g.guardian_mobile,
                    whatsapp: g.guardian_whatsapp_number,
                    cnic: g.guardian_cnic,
                    email: g.guardian_email,
                    profession: g.guardian_profession,
                    id: g.id,
                    relation: 'guardian'
                });
            }
            
            // Display each entry
            for(var j = 0; j < entries.length; j++) {
                var entry = entries[j];
                html += '<div class="guardian-result-item" onclick="selectGuardian(' + entry.id + ', \'' + entry.relation + '\')">';
                html += '<div class="guardian-result-header">';
                html += '<div class="guardian-result-name"><i class="' + entry.icon + '"></i> ' + escapeHtml(entry.name) + '</div>';
                html += '<div class="select-guardian-badge"><i class="bi bi-check-circle"></i> Select</div>';
                html += '</div>';
                html += '<div><span class="guardian-type-badge ' + entry.typeClass + '">' + entry.type + '</span></div>';
                html += '<div class="guardian-result-details">';
                if(entry.phone) html += '<span><i class="bi bi-telephone"></i> ' + escapeHtml(entry.phone) + '</span>';
                if(entry.whatsapp) html += '<span><i class="bi bi-whatsapp"></i> ' + escapeHtml(entry.whatsapp) + '</span>';
                if(entry.cnic) html += '<span><i class="bi bi-card-text"></i> ' + escapeHtml(entry.cnic) + '</span>';
                if(entry.email) html += '<span><i class="bi bi-envelope"></i> ' + escapeHtml(entry.email) + '</span>';
                html += '</div>';
                html += '</div>';
            }
        }
        
        html += '</div></div>';
        $('#guardianSearchResults').html(html).slideDown();
        $('#guardianInfoDisplay').hide();
    }
    
    function escapeHtml(text) {
        if(!text) return '';
        return text.replace(/[&<>]/g, function(m) {
            if(m === '&') return '&amp;';
            if(m === '<') return '&lt;';
            if(m === '>') return '&gt;';
            return m;
        });
    }
    
    function selectGuardian(guardianId, relation) {
        $('#loading-bar-spinner').show();
        $.ajax({
            url: 'ajax/get_guardian_details.php',
            type: 'POST',
            data: { guardian_id: guardianId },
            dataType: 'json',
            success: function(response) {
                $('#loading-bar-spinner').hide();
                if(response.success && response.guardian) {
                    displayCompleteGuardianInfo(response.guardian);
                    $('#guardianSearchResults').slideUp();
                    $('#guardianSearchInput').val('');
                } else {
                    Swal.fire('Error', 'Could not load guardian details', 'error');
                }
            },
            error: function() {
                $('#loading-bar-spinner').hide();
                Swal.fire('Error', 'Failed to load guardian details', 'error');
            }
        });
    }
    
    function displayCompleteGuardianInfo(g) {
        $('#txtGuardianID').val(g.id);
        var html = '<div class="guardian-info-card">';
        html += '<div class="card-header-primary"><i class="bi bi-shield-lock"></i> Guardian Information <button type="button" class="btn btn-sm btn-light float-end" onclick="clearGuardianInfo();" style="padding: 2px 10px;"><i class="bi bi-x"></i> Remove</button></div>';
        html += '<div class="card-body-info"><div class="row">';
        
        // Father's Information
        if(g.father_name && g.father_name.trim() !== '') {
            html += '<div class="col-md-4"><div class="guardian-col-display"><h6><i class="bi bi-person-badge"></i> Father\'s Information</h6>';
            html += '<div class="guardian-detail-row"><div class="guardian-detail-label">Full Name:</div><div class="guardian-detail-value">' + escapeHtml(g.father_name) + '</div></div>';
            html += '<div class="guardian-detail-row"><div class="guardian-detail-label">CNIC:</div><div class="guardian-detail-value">' + (g.father_cnic ? escapeHtml(g.father_cnic) : '-') + '</div></div>';
            html += '<div class="guardian-detail-row"><div class="guardian-detail-label">Mobile Number:</div><div class="guardian-detail-value">' + (g.father_mobile ? escapeHtml(g.father_mobile) : '-') + '</div></div>';
            html += '<div class="guardian-detail-row"><div class="guardian-detail-label">WhatsApp Number:</div><div class="guardian-detail-value">' + (g.father_whatsapp_number ? escapeHtml(g.father_whatsapp_number) : '-') + '</div></div>';
            html += '<div class="guardian-detail-row"><div class="guardian-detail-label">Email Address:</div><div class="guardian-detail-value">' + (g.father_email ? escapeHtml(g.father_email) : '-') + '</div></div>';
            html += '<div class="guardian-detail-row"><div class="guardian-detail-label">Profession:</div><div class="guardian-detail-value">' + (g.father_profession ? escapeHtml(g.father_profession) : '-') + '</div></div>';
            html += '<div class="guardian-detail-row"><div class="guardian-detail-label">Education:</div><div class="guardian-detail-value">' + (g.father_education ? escapeHtml(g.father_education) : '-') + '</div></div>';
            html += '</div></div>';
        }
        
        // Mother's Information
        if(g.mother_name && g.mother_name.trim() !== '') {
            html += '<div class="col-md-4"><div class="guardian-col-display"><h6><i class="bi bi-person"></i> Mother\'s Information</h6>';
            html += '<div class="guardian-detail-row"><div class="guardian-detail-label">Full Name:</div><div class="guardian-detail-value">' + escapeHtml(g.mother_name) + '</div></div>';
            html += '<div class="guardian-detail-row"><div class="guardian-detail-label">CNIC:</div><div class="guardian-detail-value">' + (g.mother_cnic ? escapeHtml(g.mother_cnic) : '-') + '</div></div>';
            html += '<div class="guardian-detail-row"><div class="guardian-detail-label">Mobile Number:</div><div class="guardian-detail-value">' + (g.mother_mobile ? escapeHtml(g.mother_mobile) : '-') + '</div></div>';
            html += '<div class="guardian-detail-row"><div class="guardian-detail-label">WhatsApp Number:</div><div class="guardian-detail-value">' + (g.mother_whatsapp_number ? escapeHtml(g.mother_whatsapp_number) : '-') + '</div></div>';
            html += '<div class="guardian-detail-row"><div class="guardian-detail-label">Email Address:</div><div class="guardian-detail-value">' + (g.mother_email ? escapeHtml(g.mother_email) : '-') + '</div></div>';
            html += '<div class="guardian-detail-row"><div class="guardian-detail-label">Profession:</div><div class="guardian-detail-value">' + (g.mother_profession ? escapeHtml(g.mother_profession) : '-') + '</div></div>';
            html += '<div class="guardian-detail-row"><div class="guardian-detail-label">Education:</div><div class="guardian-detail-value">' + (g.mother_education ? escapeHtml(g.mother_education) : '-') + '</div></div>';
            html += '</div></div>';
        }
        
        // Guardian's Information
        if(g.guardian_name && g.guardian_name.trim() !== '') {
            html += '<div class="col-md-4"><div class="guardian-col-display"><h6><i class="bi bi-shield"></i> Guardian\'s Information</h6>';
            html += '<div class="guardian-detail-row"><div class="guardian-detail-label">Full Name:</div><div class="guardian-detail-value">' + escapeHtml(g.guardian_name) + '</div></div>';
            html += '<div class="guardian-detail-row"><div class="guardian-detail-label">CNIC:</div><div class="guardian-detail-value">' + (g.guardian_cnic ? escapeHtml(g.guardian_cnic) : '-') + '</div></div>';
            html += '<div class="guardian-detail-row"><div class="guardian-detail-label">Mobile Number:</div><div class="guardian-detail-value">' + (g.guardian_mobile ? escapeHtml(g.guardian_mobile) : '-') + '</div></div>';
            html += '<div class="guardian-detail-row"><div class="guardian-detail-label">WhatsApp Number:</div><div class="guardian-detail-value">' + (g.guardian_whatsapp_number ? escapeHtml(g.guardian_whatsapp_number) : '-') + '</div></div>';
            html += '<div class="guardian-detail-row"><div class="guardian-detail-label">Email Address:</div><div class="guardian-detail-value">' + (g.guardian_email ? escapeHtml(g.guardian_email) : '-') + '</div></div>';
            html += '<div class="guardian-detail-row"><div class="guardian-detail-label">Profession:</div><div class="guardian-detail-value">' + (g.guardian_profession ? escapeHtml(g.guardian_profession) : '-') + '</div></div>';
            html += '<div class="guardian-detail-row"><div class="guardian-detail-label">Education:</div><div class="guardian-detail-value">' + (g.guardian_education ? escapeHtml(g.guardian_education) : '-') + '</div></div>';
            html += '</div></div>';
        }
        
        // Address Information
        html += '<div class="col-12 mt-3"><div class="guardian-col-display"><h6><i class="bi bi-geo-alt"></i> Address Information</h6>';
        html += '<div class="row"><div class="col-md-6"><div class="guardian-detail-row"><div class="guardian-detail-label">Present Address:</div><div class="guardian-detail-value">' + (g.present_address ? escapeHtml(g.present_address) : '-') + '</div></div>';
        html += '<div class="guardian-detail-row"><div class="guardian-detail-label">City:</div><div class="guardian-detail-value">' + (g.present_city_name || g.present_city_id || '-') + '</div></div>';
        html += '<div class="guardian-detail-row"><div class="guardian-detail-label">Area:</div><div class="guardian-detail-value">' + (g.present_area_name || '-') + '</div></div>';
        html += '<div class="guardian-detail-row"><div class="guardian-detail-label">Province:</div><div class="guardian-detail-value">' + (g.present_province || '-') + '</div></div>';
        html += '<div class="guardian-detail-row"><div class="guardian-detail-label">Country:</div><div class="guardian-detail-value">' + (g.present_country || '-') + '</div></div></div>';
        html += '<div class="col-md-6"><div class="guardian-detail-row"><div class="guardian-detail-label">Permanent Address:</div><div class="guardian-detail-value">' + (g.permanent_address ? escapeHtml(g.permanent_address) : '-') + '</div></div>';
        html += '<div class="guardian-detail-row"><div class="guardian-detail-label">City:</div><div class="guardian-detail-value">' + (g.permanent_city_name || g.permanent_city_id || '-') + '</div></div>';
        html += '<div class="guardian-detail-row"><div class="guardian-detail-label">Area:</div><div class="guardian-detail-value">' + (g.permanent_area_name || '-') + '</div></div>';
        html += '<div class="guardian-detail-row"><div class="guardian-detail-label">Province:</div><div class="guardian-detail-value">' + (g.permanent_province || '-') + '</div></div>';
        html += '<div class="guardian-detail-row"><div class="guardian-detail-label">Country:</div><div class="guardian-detail-value">' + (g.permanent_country || '-') + '</div></div></div></div>';
        html += '</div></div>';
        
        html += '</div></div></div>';
        $('#guardianInfoDisplay').html(html).slideDown();
        
        // Auto-fill father name in student form
        if(g.father_name) $('#txtFatherName').val(g.father_name);
    }
    
    function clearGuardianInfo() { 
        $('#guardianSearchInput').val(''); 
        $('#guardianSearchResults').hide(); 
        $('#guardianInfoDisplay').hide(); 
        $('#txtGuardianID').val('0'); 
    }
    
    function removeStudentPicture() { 
        $('#StudentPicture').val(''); 
        $('#currentImagePreview').hide(); 
        $('#studentImagePreview').attr('src',''); 
        $('#existingPicture').val('');
        Swal.fire('Info','Picture removed','info'); 
    }
    
    function validateImageSize(input) { 
        if(input.files && input.files[0]) { 
            var fs = input.files[0].size; 
            if(fs > 2*1024*1024) { 
                $('#ImageSize').text('Max 2MB').show(); 
                input.value=''; 
                $('#currentImagePreview').hide(); 
            } else { 
                $('#ImageSize').hide(); 
                var reader = new FileReader(); 
                reader.onload = function(e) { 
                    $('#studentImagePreview').attr('src',e.target.result); 
                    $('#currentImagePreview').show(); 
                }; 
                reader.readAsDataURL(input.files[0]); 
            } 
        } 
    }
    
    function updateStudentInfo() {
        var requiredFields = ['#ddlSchoolClasses', '#ddlSchoolClassSection', '#txtAdmissionDate', '#txtStudentName', '#txtFatherName', '#txtDateOfBirth', '#ddlStudentCategoryIdFk', '#ddlReligionIdFk', '#ddlGenderIdFk', '#txtAdmissionEffectiveDate'];
        var isValid = true;
        
        for (var i = 0; i < requiredFields.length; i++) {
            var field = $(requiredFields[i]);
            var fieldValue = field.val();
            if (!fieldValue || fieldValue == '0') {
                field.addClass('is-invalid');
                isValid = false;
            } else {
                field.removeClass('is-invalid');
            }
        }
        
        if (!isValid) {
            Swal.fire('Error', 'Please fill all required fields (marked with *)', 'error');
            return;
        }
        
        var formData = new FormData(document.getElementById('admissionForm'));
        formData.append('student_id', $('#txtStudentID').val());
        formData.append('guardian_id', $('#txtGuardianID').val());
        formData.append('existing_picture', $('#existingPicture').val());
        
        $('#loading-bar-spinner').show();
        
        $.ajax({
            url: 'ajax/update_student.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(data) {
                $('#loading-bar-spinner').hide();
                if (data.success) {
                    Swal.fire('Success', data.message, 'success').then(() => {
                        window.location.href = 'student_list.php';
                    });
                } else {
                    Swal.fire('Error', data.message, 'error');
                }
            },
            error: function(xhr, status, error) {
                $('#loading-bar-spinner').hide();
                console.log('Response:', xhr.responseText);
                Swal.fire('Error', 'An error occurred: ' + error, 'error');
            }
        });
    }
    
    function resetForm() { 
        // Don't completely reset, just revert to original data
        location.reload();
    }
</script>
</body>
</html>