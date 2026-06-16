<?php
// edit_guardian.php
include '../connect.php';

// Get guardian ID from URL
$guardian_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($guardian_id <= 0) {
    header("Location: guardian_list.php");
    exit;
}

// Fetch guardian data
$sql = "SELECT sg.*, 
        pc.name as present_city_name, pa.name as present_area_name,
        perc.name as permanent_city_name, pera.name as permanent_area_name
        FROM student_guardians sg 
        LEFT JOIN cities pc ON sg.present_city_id = pc.id 
        LEFT JOIN areas pa ON sg.present_area_id = pa.id
        LEFT JOIN cities perc ON sg.permanent_city_id = perc.id 
        LEFT JOIN areas pera ON sg.permanent_area_id = pera.id
        WHERE sg.id = $guardian_id";

$result = $conn->query($sql);

if (!$result || $result->num_rows == 0) {
    header("Location: guardian_list.php");
    exit;
}

$guardian = $result->fetch_assoc();

// Fetch cities for dropdowns
$citySql = "SELECT c.id, c.name, p.name as province_name, p.country_id, cnt.name as country_name 
            FROM cities c
            INNER JOIN provinces p ON c.province_id = p.id
            INNER JOIN countries cnt ON p.country_id = cnt.id
            WHERE c.status = 'Active' AND p.status = 'Active' AND cnt.status = 'Active'
            ORDER BY cnt.name, p.name, c.name";
$cityResult = $conn->query($citySql);

// Fetch mobile operators
$operatorSql = "SELECT id, name FROM mobile_operators WHERE status = 'Active' ORDER BY name";
$operatorResult = $conn->query($operatorSql);

// Fetch areas for present city if selected
$presentAreas = [];
if ($guardian['present_city_id']) {
    $areaSql = "SELECT id, name FROM areas WHERE city_id = " . $guardian['present_city_id'] . " AND status = 'Active' ORDER BY name";
    $areaRes = $conn->query($areaSql);
    while ($area = $areaRes->fetch_assoc()) {
        $presentAreas[] = $area;
    }
}

// Fetch areas for permanent city if selected
$permanentAreas = [];
if ($guardian['permanent_city_id']) {
    $areaSql = "SELECT id, name FROM areas WHERE city_id = " . $guardian['permanent_city_id'] . " AND status = 'Active' ORDER BY name";
    $areaRes = $conn->query($areaSql);
    while ($area = $areaRes->fetch_assoc()) {
        $permanentAreas[] = $area;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <title>Edit Guardian - Guardian Management</title>
  <?php include '../parts/links1.php'; ?>
  <?php include '../parts/style.php'; ?>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <!-- SweetAlert2 for better alerts -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <style>
    .form-section {
      background: #f8f9fa;
      border-radius: 8px;
      padding: 15px;
      margin-bottom: 15px;
      border-left: 4px solid #4e73df;
    }
    .form-section h4 {
      color: #2c3e50;
      margin-bottom: 15px;
      font-weight: 700;
      font-size: 1.1rem;
    }
    .required-field::after {
      content: "*";
      color: red;
      margin-left: 4px;
    }
    .communication-checkbox {
      display: flex;
      gap: 15px;
      margin-top: 5px;
    }
    .communication-checkbox label {
      margin-right: 15px;
      cursor: pointer;
      font-weight: 600;
      color: #2c3e50;
      display: flex;
      align-items: center;
      gap: 8px;
    }
    /* Increased checkbox size */
    .communication-checkbox input[type="checkbox"],
    .form-check-input {
      width: 20px;
      height: 20px;
      margin-top: 0;
      cursor: pointer;
      accent-color: #4e73df;
      transform: scale(1.1);
    }
    .form-check {
      display: flex;
      align-items: center;
    }
    .form-check-label {
      font-weight: 600;
      color: #2c3e50;
      margin-left: 8px;
      cursor: pointer;
    }
    .form-control, .form-select {
      border-radius: 6px;
      border: 1px solid #d1d5db;
      font-weight: 500;
      color: #1f2937 !important;
      background-color: #ffffff;
    }
    /* Dark text color for dropdown selected values */
    select.form-select, select.form-control {
      color: #1f2937 !important;
      background-color: #ffffff;
    }
    select.form-select option, select.form-control option {
      color: #1f2937;
      background-color: #ffffff;
    }
    /* Ensure selected value is dark */
    select.form-select:focus, select.form-control:focus {
      color: #1f2937 !important;
    }
    /* Remove default blue color for selected options */
    select.form-select option:checked, select.form-control option:checked {
      background-color: #e8f0fe;
      color: #1f2937;
    }
    .form-control:focus, .form-select:focus {
      border-color: #4e73df;
      box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
    }
    .address-box {
      background: white;
      border-radius: 8px;
      padding: 12px;
      height: 100%;
      border: 1px solid #e3e6f0;
    }
    .address-box h5 {
      color: #2c3e50;
      font-weight: 700;
      margin-bottom: 12px;
      font-size: 1rem;
      display: flex;
      align-items: center;
      justify-content: space-between;
      flex-wrap: wrap;
      gap: 10px;
    }
    .address-box h5 .same-address-check {
      font-size: 0.85rem;
      font-weight: 600;
      display: flex;
      align-items: center;
      gap: 8px;
    }
    .address-box h5 .same-address-check input {
      width: 18px;
      height: 18px;
      margin: 0;
      cursor: pointer;
      accent-color: #4e73df;
    }
    .guardian-col {
      background: white;
      border-radius: 8px;
      padding: 12px;
      height: 100%;
      border: 1px solid #e3e6f0;
    }
    .guardian-col h5 {
      color: #2c3e50;
      font-weight: 700;
      border-bottom: 2px solid #4e73df;
      padding-bottom: 8px;
      margin-bottom: 12px;
      font-size: 0.95rem;
    }
    .location-readonly {
      background-color: #f3f4f6;
      cursor: not-allowed;
      font-weight: 500;
      color: #1f2937 !important;
    }
    .form-group {
      margin-bottom: 12px;
    }
    .form-group label {
      font-weight: 700;
      color: #2c3e50;
      margin-bottom: 5px;
      font-size: 0.85rem;
    }
    .card-body {
      padding: 20px;
    }
    .btn {
      font-weight: 600;
      padding: 8px 25px;
    }
    textarea.form-control {
      resize: vertical;
    }
    select.form-control, input.form-control {
      font-size: 0.9rem;
    }
    .row {
      margin-bottom: 0;
    }
    .mb-3 {
      margin-bottom: 10px !important;
    }
    /* Remove spinner from number input */
    input[type=number]::-webkit-inner-spin-button, 
    input[type=number]::-webkit-outer-spin-button {
      -webkit-appearance: none;
      margin: 0;
    }
    input[type=number] {
      -moz-appearance: textfield;
    }
    /* Larger checkboxes for communication section */
    .communication-checkbox input[type="checkbox"] {
      width: 18px;
      height: 18px;
      transform: scale(1.1);
    }
    /* Disable autocomplete styles */
    input, select, textarea {
      -webkit-autofill: none;
      transition: background-color 5000s ease-in-out 0s;
    }
    
    /* Mini button styles */
    .add-btn-mini {
      background: #4e73df;
      border: none;
      color: white;
      font-size: 11px;
      padding: 3px 10px;
      border-radius: 15px;
      cursor: pointer;
      transition: all 0.2s ease;
      display: inline-flex;
      align-items: center;
      gap: 4px;
    }
    .add-btn-mini:hover {
      background: #2e59d9;
      transform: translateY(-1px);
    }
    .add-btn-mini i {
      font-size: 10px;
    }
    
    /* Right alignment for button container */
    .field-header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 5px;
    }
    .field-header label {
      margin-bottom: 0;
      font-weight: 700;
      color: #2c3e50;
      font-size: 0.85rem;
    }
    .field-header .add-actions {
      display: flex;
      gap: 5px;
    }
  </style>
</head>
<body>
  <div class="container-scroller">
    <!-- Navbar -->
    <?php include '../parts/navbar.php'; ?>
    
    <div class="container-fluid page-body-wrapper">
      <!-- Sidebars -->
      <?php include '../parts/setting.php'; ?>
      <?php include '../parts/right_sidebar.php'; ?>
      <?php include '../parts/left_sidebar.php'; ?>

      <div class="main-panel">
        <div class="content-wrapper" style="padding: 15px;">
          <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
              <div class="card">

                <div class="card-body">
                  <form id="guardianEditForm" method="POST" action="ajax/update_guardian.php" autocomplete="off">
                    <input type="hidden" name="guardian_id" value="<?php echo $guardian['id']; ?>">

                    <!-- Submit Button -->
                    <div class="d-flex justify-content-end mb-3" style="gap: 5px;">
                        <a href="guardian_list.php" class="btn btn-info px-3 py-2">
                            <i class="ti-arrow-left"></i> Go Back
                        </a>
                        <button type="submit" class="btn btn-primary px-3 py-2">
                            <i class="ti-save"></i> Update Guardian
                        </button>
                        <button type="reset" class="btn btn-secondary px-3 py-2">
                            <i class="ti-reload"></i> Reset
                        </button>
                    </div>

                    <!-- ==================== ADDRESS SECTION ==================== -->
                    <div class="form-section">
                      <h4><i class="ti-location-pin"></i> Address Information</h4>
                      <div class="row">
                        <!-- Present Address Column -->
                        <div class="col-md-6">
                          <div class="address-box">
                            <h5>Present Address</h5>
                            <div class="form-group">
                              <div class="field-header">
                                <label class="required-field">Student Address</label>
                              </div>
                              <textarea class="form-control" name="present_address" rows="2" placeholder="Enter complete present address" required autocomplete="off"><?php echo htmlspecialchars($guardian['present_address']); ?></textarea>
                            </div>
                            <div class="row">
                              <div class="col-md-6">
                                <div class="form-group">
                                  <div class="field-header">
                                    <label>Country</label>
                                  </div>
                                  <input type="text" class="form-control location-readonly" name="present_country" id="present_country" readonly placeholder="Country" value="<?php echo htmlspecialchars($guardian['present_country']); ?>" autocomplete="off">
                                </div>
                              </div>
                              <div class="col-md-6">
                                <div class="form-group">
                                  <div class="field-header">
                                    <label>Province / State</label>
                                  </div>
                                  <input type="text" class="form-control location-readonly" name="present_province" id="present_province" readonly placeholder="Province" value="<?php echo htmlspecialchars($guardian['present_province']); ?>" autocomplete="off">
                                </div>
                              </div>
                            </div>
                            <div class="row">
                              <div class="col-md-6">
                                <div class="form-group">
                                  <div class="field-header">
                                    <label class="required-field">Select City</label>
                                    <div class="add-actions">
                                      <button type="button" class="add-btn-mini" onclick="window.open('../setting_lms/city.php', '_blank');">
                                        <i class="bi bi-plus"></i> Add City
                                      </button>
                                    </div>
                                  </div>
                                  <select class="form-control" name="present_city_id" id="present_city_id" required autocomplete="off">
                                    <option value="">Select City</option>
                                    <?php
                                      if ($cityResult && $cityResult->num_rows > 0) {
                                        $cityResult->data_seek(0);
                                        while ($city = $cityResult->fetch_assoc()) {
                                          $selected = ($guardian['present_city_id'] == $city['id']) ? 'selected' : '';
                                          echo "<option value='" . $city['id'] . "' $selected 
                                                data-province='" . htmlspecialchars($city['province_name']) . "'
                                                data-country-id='" . $city['country_id'] . "'
                                                data-country='" . htmlspecialchars($city['country_name']) . "'>" 
                                                . htmlspecialchars($city['name']) . "</option>";
                                        }
                                      }
                                    ?>
                                  </select>
                                </div>
                              </div>
                              <div class="col-md-6">
                                <div class="form-group">
                                  <div class="field-header">
                                    <label>Area</label>
                                    <div class="add-actions">
                                      <button type="button" class="add-btn-mini" onclick="window.open('../setting_lms/area.php', '_blank');">
                                        <i class="bi bi-plus"></i> Add Area
                                      </button>
                                    </div>
                                  </div>
                                  <select class="form-control" name="present_area_id" id="present_area_id" autocomplete="off">
                                    <option value="">Select Area</option>
                                    <?php
                                      foreach ($presentAreas as $area) {
                                        $selected = ($guardian['present_area_id'] == $area['id']) ? 'selected' : '';
                                        echo "<option value='" . $area['id'] . "' $selected>" . htmlspecialchars($area['name']) . "</option>";
                                      }
                                    ?>
                                  </select>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                        
                        <!-- Permanent Address Column -->
                        <div class="col-md-6">
                          <div class="address-box">
                            <h5>
                              Permanent Address
                              <label class="same-address-check">
                                <input type="checkbox" id="sameAsPresent" autocomplete="off"> Same as Present Address
                              </label>
                            </h5>
                            <div id="permanentAddressFields">
                              <div class="form-group">
                                <div class="field-header">
                                  <label class="required-field">Student Address</label>
                                </div>
                                <textarea class="form-control" name="permanent_address" rows="2" placeholder="Enter complete permanent address" autocomplete="off"><?php echo htmlspecialchars($guardian['permanent_address']); ?></textarea>
                              </div>
                              <div class="row">
                                <div class="col-md-6">
                                  <div class="form-group">
                                    <div class="field-header">
                                      <label>Country</label>
                                    </div>
                                    <input type="text" class="form-control location-readonly" name="permanent_country" id="permanent_country" readonly placeholder="Country" value="<?php echo htmlspecialchars($guardian['permanent_country']); ?>" autocomplete="off">
                                  </div>
                                </div>
                                <div class="col-md-6">
                                  <div class="form-group">
                                    <div class="field-header">
                                      <label>Province / State</label>
                                    </div>
                                    <input type="text" class="form-control location-readonly" name="permanent_province" id="permanent_province" readonly placeholder="Province" value="<?php echo htmlspecialchars($guardian['permanent_province']); ?>" autocomplete="off">
                                  </div>
                                </div>
                              </div>
                              <div class="row">
                                <div class="col-md-6">
                                  <div class="form-group">
                                    <div class="field-header">
                                      <label class="required-field">Select City</label>
                                      <div class="add-actions">
                                        <button type="button" class="add-btn-mini" onclick="window.open('../setting_lms/city.php', '_blank');">
                                          <i class="bi bi-plus"></i> Add City
                                        </button>
                                      </div>
                                    </div>
                                    <select class="form-control" name="permanent_city_id" id="permanent_city_id" autocomplete="off">
                                      <option value="">Select City</option>
                                      <?php
                                        if ($cityResult && $cityResult->num_rows > 0) {
                                          $cityResult->data_seek(0);
                                          while ($city = $cityResult->fetch_assoc()) {
                                            $selected = ($guardian['permanent_city_id'] == $city['id']) ? 'selected' : '';
                                            echo "<option value='" . $city['id'] . "' $selected
                                                  data-province='" . htmlspecialchars($city['province_name']) . "'
                                                  data-country-id='" . $city['country_id'] . "'
                                                  data-country='" . htmlspecialchars($city['country_name']) . "'>" 
                                                  . htmlspecialchars($city['name']) . "</option>";
                                          }
                                        }
                                      ?>
                                    </select>
                                  </div>
                                </div>
                                <div class="col-md-6">
                                  <div class="form-group">
                                    <div class="field-header">
                                      <label>Area</label>
                                      <div class="add-actions">
                                        <button type="button" class="add-btn-mini" onclick="window.open('../setting_lms/area.php', '_blank');">
                                          <i class="bi bi-plus"></i> Add Area
                                        </button>
                                      </div>
                                    </div>
                                    <select class="form-control" name="permanent_area_id" id="permanent_area_id" autocomplete="off">
                                      <option value="">Select Area</option>
                                      <?php
                                        foreach ($permanentAreas as $area) {
                                          $selected = ($guardian['permanent_area_id'] == $area['id']) ? 'selected' : '';
                                          echo "<option value='" . $area['id'] . "' $selected>" . htmlspecialchars($area['name']) . "</option>";
                                        }
                                      ?>
                                    </select>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>

                    <!-- ==================== GUARDIAN SECTION ==================== -->
                    <div class="form-section">
                      <h4><i class="ti-user"></i> Guardian Information</h4>
                      <div class="row">
                        <!-- Father's Information Column -->
                        <div class="col-md-4">
                          <div class="guardian-col">
                            <h5>Father's Information</h5>
                            
                            <!-- 1. Father Name -->
                            <div class="form-group">
                              <div class="field-header">
                                <label class="required-field">Father Name</label>
                              </div>
                              <input type="text" class="form-control" name="father_name" placeholder="Enter father's full name" required autocomplete="off" value="<?php echo htmlspecialchars($guardian['father_name']); ?>">
                            </div>
                            
                            <!-- 2. Father CNIC with auto-formatting -->
                            <div class="form-group">
                              <div class="field-header">
                                <label>Father CNIC</label>
                              </div>
                              <input type="text" class="form-control cnic-input" name="father_cnic" placeholder="33133-6767676-5" maxlength="15" autocomplete="off" value="<?php echo htmlspecialchars($guardian['father_cnic']); ?>">
                            </div>
                            
                            <!-- 3. Mobile Number & Mobile Operator - Same Row -->
                            <div class="row">
                              <div class="col-md-6">
                                <div class="form-group">
                                  <div class="field-header">
                                    <label>Mobile Number</label>
                                  </div>
                                  <input type="tel" class="form-control mobile-input" name="father_mobile" placeholder="923001234567" maxlength="12" pattern="[0-9]{12}" title="Please enter 12 digits" autocomplete="off" value="<?php echo htmlspecialchars($guardian['father_mobile']); ?>">
                                </div>
                              </div>
                              <div class="col-md-6">
                                <div class="form-group">
                                  <div class="field-header">
                                    <label>Mobile Operator</label>
                                    <div class="add-actions">
                                      <button type="button" class="add-btn-mini" onclick="window.open('../setting_lms/mobile_operator.php', '_blank');">
                                        <i class="bi bi-plus"></i> Add
                                      </button>
                                    </div>
                                  </div>
                                  <select class="form-control" name="father_mobile_operator" autocomplete="off">
                                    <option value="">Select Operator</option>
                                    <?php
                                      if ($operatorResult && $operatorResult->num_rows > 0) {
                                        $operatorResult->data_seek(0);
                                        while ($operator = $operatorResult->fetch_assoc()) {
                                          $selected = ($guardian['father_mobile_operator'] == $operator['id']) ? 'selected' : '';
                                          echo "<option value='" . $operator['id'] . "' $selected>" . htmlspecialchars($operator['name']) . "</option>";
                                        }
                                      }
                                    ?>
                                  </select>
                                </div>
                              </div>
                            </div>
                            
                            <!-- 4. For Communication -->
                            <div class="form-group">
                              <div class="field-header">
                                <label>For Communication</label>
                              </div>
                              <div class="communication-checkbox">
                                <label><input type="checkbox" name="father_sms" value="1" <?php echo $guardian['father_sms'] ? 'checked' : ''; ?> autocomplete="off"> FOR SMS</label>
                                <label><input type="checkbox" name="father_whatsapp" value="1" <?php echo $guardian['father_whatsapp'] ? 'checked' : ''; ?> autocomplete="off"> FOR WHATSAPP SMS</label>
                              </div>
                            </div>
                            
                            <!-- 5. WhatsApp Number -->
                            <div class="form-group">
                              <div class="field-header">
                                <label>WhatsApp Number</label>
                              </div>
                              <input type="tel" class="form-control mobile-input" name="father_whatsapp_number" placeholder="923001234567" maxlength="12" pattern="[0-9]{12}" autocomplete="off" value="<?php echo htmlspecialchars($guardian['father_whatsapp_number']); ?>">
                            </div>
                            
                            <!-- 6. Father Profession & Father Education - Same Row -->
                            <div class="row">
                              <div class="col-md-6">
                                <div class="form-group">
                                  <div class="field-header">
                                    <label>Father Profession</label>
                                  </div>
                                  <input type="text" class="form-control" name="father_profession" placeholder="Enter father's profession" autocomplete="off" value="<?php echo htmlspecialchars($guardian['father_profession']); ?>">
                                </div>
                              </div>
                              <div class="col-md-6">
                                <div class="form-group">
                                  <div class="field-header">
                                    <label>Father Education</label>
                                  </div>
                                  <input type="text" class="form-control" name="father_education" placeholder="Enter father's education" autocomplete="off" value="<?php echo htmlspecialchars($guardian['father_education']); ?>">
                                </div>
                              </div>
                            </div>
                            
                            <!-- 7. Father Email -->
                            <div class="form-group">
                              <div class="field-header">
                                <label>Email Address</label>
                              </div>
                              <input type="email" class="form-control" name="father_email" placeholder="abc@example.com" autocomplete="off" value="<?php echo htmlspecialchars($guardian['father_email']); ?>">
                            </div>
                          </div>
                        </div>

                        <!-- Mother's Information Column -->
                        <div class="col-md-4">
                          <div class="guardian-col">
                            <h5>Mother's Information</h5>
                            
                            <!-- 1. Mother Name -->
                            <div class="form-group">
                              <div class="field-header">
                                <label class="required-field">Mother Name</label>
                              </div>
                              <input type="text" class="form-control" name="mother_name" placeholder="Enter mother's full name" required autocomplete="off" value="<?php echo htmlspecialchars($guardian['mother_name']); ?>">
                            </div>
                            
                            <!-- 2. Mother CNIC with auto-formatting -->
                            <div class="form-group">
                              <div class="field-header">
                                <label>Mother CNIC</label>
                              </div>
                              <input type="text" class="form-control cnic-input" name="mother_cnic" placeholder="33133-6767676-5" maxlength="15" autocomplete="off" value="<?php echo htmlspecialchars($guardian['mother_cnic']); ?>">
                            </div>
                            
                            <!-- 3. Mobile Number & Mobile Operator - Same Row -->
                            <div class="row">
                              <div class="col-md-6">
                                <div class="form-group">
                                  <div class="field-header">
                                    <label>Mobile Number</label>
                                  </div>
                                  <input type="tel" class="form-control mobile-input" name="mother_mobile" placeholder="923001234567" maxlength="12" pattern="[0-9]{12}" autocomplete="off" value="<?php echo htmlspecialchars($guardian['mother_mobile']); ?>">
                                </div>
                              </div>
                              <div class="col-md-6">
                                <div class="form-group">
                                  <div class="field-header">
                                    <label>Mobile Operator</label>
                                    <div class="add-actions">
                                      <button type="button" class="add-btn-mini" onclick="window.open('../setting_lms/mobile_operator.php', '_blank');">
                                        <i class="bi bi-plus"></i> Add
                                      </button>
                                    </div>
                                  </div>
                                  <select class="form-control" name="mother_mobile_operator" autocomplete="off">
                                    <option value="">Select Operator</option>
                                    <?php
                                      if ($operatorResult && $operatorResult->num_rows > 0) {
                                        $operatorResult->data_seek(0);
                                        while ($operator = $operatorResult->fetch_assoc()) {
                                          $selected = ($guardian['mother_mobile_operator'] == $operator['id']) ? 'selected' : '';
                                          echo "<option value='" . $operator['id'] . "' $selected>" . htmlspecialchars($operator['name']) . "</option>";
                                        }
                                      }
                                    ?>
                                  </select>
                                </div>
                              </div>
                            </div>
                            
                            <!-- 4. For Communication -->
                            <div class="form-group">
                              <div class="field-header">
                                <label>For Communication</label>
                              </div>
                              <div class="communication-checkbox">
                                <label><input type="checkbox" name="mother_sms" value="1" <?php echo $guardian['mother_sms'] ? 'checked' : ''; ?> autocomplete="off"> FOR SMS</label>
                                <label><input type="checkbox" name="mother_whatsapp" value="1" <?php echo $guardian['mother_whatsapp'] ? 'checked' : ''; ?> autocomplete="off"> FOR WHATSAPP SMS</label>
                              </div>
                            </div>
                            
                            <!-- 5. WhatsApp Number -->
                            <div class="form-group">
                              <div class="field-header">
                                <label>WhatsApp Number</label>
                              </div>
                              <input type="tel" class="form-control mobile-input" name="mother_whatsapp_number" placeholder="923001234567" maxlength="12" pattern="[0-9]{12}" autocomplete="off" value="<?php echo htmlspecialchars($guardian['mother_whatsapp_number']); ?>">
                            </div>
                            
                            <!-- 6. Mother Profession & Mother Education - Same Row -->
                            <div class="row">
                              <div class="col-md-6">
                                <div class="form-group">
                                  <div class="field-header">
                                    <label>Mother Profession</label>
                                  </div>
                                  <input type="text" class="form-control" name="mother_profession" placeholder="Enter mother's profession" autocomplete="off" value="<?php echo htmlspecialchars($guardian['mother_profession']); ?>">
                                </div>
                              </div>
                              <div class="col-md-6">
                                <div class="form-group">
                                  <div class="field-header">
                                    <label>Mother Education</label>
                                  </div>
                                  <input type="text" class="form-control" name="mother_education" placeholder="Enter mother's education" autocomplete="off" value="<?php echo htmlspecialchars($guardian['mother_education']); ?>">
                                </div>
                              </div>
                            </div>
                            
                            <!-- 7. Mother Email -->
                            <div class="form-group">
                              <div class="field-header">
                                <label>Email Address</label>
                              </div>
                              <input type="email" class="form-control" name="mother_email" placeholder="abc@example.com" autocomplete="off" value="<?php echo htmlspecialchars($guardian['mother_email']); ?>">
                            </div>
                          </div>
                        </div>

                        <!-- Guardian's Information Column -->
                        <div class="col-md-4">
                          <div class="guardian-col">
                            <h5>Guardian's Information</h5>
                            
                            <!-- 1. Guardian Name -->
                            <div class="form-group">
                              <div class="field-header">
                                <label>Guardian Name</label>
                              </div>
                              <input type="text" class="form-control" name="guardian_name" placeholder="Enter guardian's full name" autocomplete="off" value="<?php echo htmlspecialchars($guardian['guardian_name']); ?>">
                            </div>
                            
                            <!-- 2. Guardian CNIC with auto-formatting -->
                            <div class="form-group">
                              <div class="field-header">
                                <label>Guardian CNIC</label>
                              </div>
                              <input type="text" class="form-control cnic-input" name="guardian_cnic" placeholder="33133-6767676-5" maxlength="15" autocomplete="off" value="<?php echo htmlspecialchars($guardian['guardian_cnic']); ?>">
                            </div>
                            
                            <!-- 3. Mobile Number & Mobile Operator - Same Row -->
                            <div class="row">
                              <div class="col-md-6">
                                <div class="form-group">
                                  <div class="field-header">
                                    <label>Mobile Number</label>
                                  </div>
                                  <input type="tel" class="form-control mobile-input" name="guardian_mobile" placeholder="923001234567" maxlength="12" pattern="[0-9]{12}" autocomplete="off" value="<?php echo htmlspecialchars($guardian['guardian_mobile']); ?>">
                                </div>
                              </div>
                              <div class="col-md-6">
                                <div class="form-group">
                                  <div class="field-header">
                                    <label>Mobile Operator</label>
                                    <div class="add-actions">
                                      <button type="button" class="add-btn-mini" onclick="window.open('../setting_lms/mobile_operator.php', '_blank');">
                                        <i class="bi bi-plus"></i> Add
                                      </button>
                                    </div>
                                  </div>
                                  <select class="form-control" name="guardian_mobile_operator" autocomplete="off">
                                    <option value="">Select Operator</option>
                                    <?php
                                      if ($operatorResult && $operatorResult->num_rows > 0) {
                                        $operatorResult->data_seek(0);
                                        while ($operator = $operatorResult->fetch_assoc()) {
                                          $selected = ($guardian['guardian_mobile_operator'] == $operator['id']) ? 'selected' : '';
                                          echo "<option value='" . $operator['id'] . "' $selected>" . htmlspecialchars($operator['name']) . "</option>";
                                        }
                                      }
                                    ?>
                                  </select>
                                </div>
                              </div>
                            </div>
                            
                            <!-- 4. For Communication -->
                            <div class="form-group">
                              <div class="field-header">
                                <label>For Communication</label>
                              </div>
                              <div class="communication-checkbox">
                                <label><input type="checkbox" name="guardian_sms" value="1" <?php echo $guardian['guardian_sms'] ? 'checked' : ''; ?> autocomplete="off"> FOR SMS</label>
                                <label><input type="checkbox" name="guardian_whatsapp" value="1" <?php echo $guardian['guardian_whatsapp'] ? 'checked' : ''; ?> autocomplete="off"> FOR WHATSAPP SMS</label>
                              </div>
                            </div>
                            
                            <!-- 5. WhatsApp Number -->
                            <div class="form-group">
                              <div class="field-header">
                                <label>WhatsApp Number</label>
                              </div>
                              <input type="tel" class="form-control mobile-input" name="guardian_whatsapp_number" placeholder="923001234567" maxlength="12" pattern="[0-9]{12}" autocomplete="off" value="<?php echo htmlspecialchars($guardian['guardian_whatsapp_number']); ?>">
                            </div>
                            
                            <!-- 6. Guardian Profession & Guardian Education - Same Row -->
                            <div class="row">
                              <div class="col-md-6">
                                <div class="form-group">
                                  <div class="field-header">
                                    <label>Guardian Profession</label>
                                  </div>
                                  <input type="text" class="form-control" name="guardian_profession" placeholder="Enter guardian's profession" autocomplete="off" value="<?php echo htmlspecialchars($guardian['guardian_profession']); ?>">
                                </div>
                              </div>
                              <div class="col-md-6">
                                <div class="form-group">
                                  <div class="field-header">
                                    <label>Guardian Education</label>
                                  </div>
                                  <input type="text" class="form-control" name="guardian_education" placeholder="Enter guardian's education" autocomplete="off" value="<?php echo htmlspecialchars($guardian['guardian_education']); ?>">
                                </div>
                              </div>
                            </div>
                            
                            <!-- 7. Guardian Email -->
                            <div class="form-group">
                              <div class="field-header">
                                <label>Email Address</label>
                              </div>
                              <input type="email" class="form-control" name="guardian_email" placeholder="abc@example.com" autocomplete="off" value="<?php echo htmlspecialchars($guardian['guardian_email']); ?>">
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>

                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- footer -->
        <?php include '../parts/footer.php'; ?>
      </div>
    </div>
  </div>

  <!-- Scripts -->
  <?php include '../parts/links2.php'; ?>

  <script>
    // Disable browser autocomplete for all form elements (additional JavaScript method)
    document.querySelectorAll('input, select, textarea').forEach(function(element) {
      element.setAttribute('autocomplete', 'off');
    });
    
    // ==================== CNIC AUTO-FORMATTING (FIXED) ====================
    // Function to format CNIC as user types
    function formatCNIC(input) {
        // Get cursor position before making changes
        const cursorPos = input.selectionStart;
        const oldValue = input.value;
        
        // Remove all non-digits
        let digits = oldValue.replace(/\D/g, '');
        
        // Limit to 13 digits
        if (digits.length > 13) {
            digits = digits.slice(0, 13);
        }
        
        // Format with dashes: XXXXX-XXXXXXX-X
        let formatted = '';
        if (digits.length > 0) {
            formatted = digits.slice(0, 5);
            if (digits.length >= 5) {
                formatted += '-' + digits.slice(5, 12);
                if (digits.length >= 12) {
                    formatted += '-' + digits.slice(12, 13);
                }
            }
        }
        
        // Only update if value actually changed to prevent infinite loop
        if (formatted !== oldValue) {
            input.value = formatted;
            
            // Calculate new cursor position
            let newCursorPos = cursorPos;
            
            // Adjust cursor position based on added/removed dashes
            const oldDashCount = (oldValue.match(/-/g) || []).length;
            const newDashCount = (formatted.match(/-/g) || []).length;
            
            if (newDashCount > oldDashCount) {
                // Dash was added
                newCursorPos++;
            } else if (newDashCount < oldDashCount) {
                // Dash was removed
                newCursorPos--;
            }
            
            // Ensure cursor position is within bounds
            newCursorPos = Math.min(newCursorPos, formatted.length);
            newCursorPos = Math.max(newCursorPos, 0);
            
            // Restore cursor position
            input.setSelectionRange(newCursorPos, newCursorPos);
        }
    }

    // Apply CNIC formatting to all CNIC inputs
    document.querySelectorAll('.cnic-input').forEach(input => {
        // Remove existing listeners to avoid duplicates
        input.removeEventListener('input', formatCNICHandler);
        input.removeEventListener('keydown', preventInvalidKeys);
        input.removeEventListener('paste', pasteHandler);
        
        // Create handlers
        function formatCNICHandler(e) {
            formatCNIC(this);
        }
        
        function preventInvalidKeys(e) {
            // Allow: backspace, delete, tab, escape, enter, home, end, left, right, up, down
            if (e.keyCode === 8 || e.keyCode === 46 || e.keyCode === 9 || e.keyCode === 27 || 
                e.keyCode === 13 || e.keyCode === 35 || e.keyCode === 36 || e.keyCode === 37 || 
                e.keyCode === 38 || e.keyCode === 39 || e.keyCode === 40) {
                return;
            }
            // Allow Ctrl+A, Ctrl+C, Ctrl+V, Ctrl+X
            if ((e.ctrlKey === true || e.metaKey === true) && (e.keyCode === 65 || e.keyCode === 67 || e.keyCode === 86 || e.keyCode === 88)) {
                return;
            }
            // Allow digits only
            if (e.keyCode < 48 || e.keyCode > 57) {
                e.preventDefault();
            }
        }
        
        function pasteHandler(e) {
            setTimeout(() => {
                formatCNIC(this);
            }, 10);
        }
        
        // Add event listeners
        input.addEventListener('input', formatCNICHandler);
        input.addEventListener('keydown', preventInvalidKeys);
        input.addEventListener('paste', pasteHandler);
    });

    // ==================== MOBILE NUMBER - DIGITS ONLY ====================
    // Function to allow only digits for mobile number
    function restrictToDigits(input) {
      // Remove all non-digits
      let value = input.value.replace(/\D/g, '');
      
      // Limit to 12 digits (for Pakistan format 923001234567)
      if (value.length > 12) {
        value = value.slice(0, 12);
      }
      
      input.value = value;
    }
    
    // Apply digit restriction to all mobile inputs
    document.querySelectorAll('.mobile-input').forEach(input => {
      input.addEventListener('input', function() {
        restrictToDigits(this);
      });
      
      input.addEventListener('keypress', function(e) {
        // Allow only digits (0-9)
        const charCode = e.which ? e.which : e.keyCode;
        if (charCode < 48 || charCode > 57) {
          e.preventDefault();
        }
      });
      
      input.addEventListener('paste', function(e) {
        setTimeout(() => {
          restrictToDigits(this);
        }, 10);
      });
    });
    
    // ==================== EXISTING FUNCTIONS ====================
    // Function to load areas based on selected city
    function loadAreas(cityId, targetAreaSelect, selectedAreaId, callback) {
      if (cityId) {
        $(targetAreaSelect).html('<option value="">Loading...</option>');
        $.ajax({
          url: 'ajax/get_areas_by_city.php',
          type: 'POST',
          data: { city_id: cityId },
          dataType: 'json',
          success: function(data) {
            $(targetAreaSelect).html('<option value="">Select Area</option>');
            if (data.areas && data.areas.length > 0) {
              $.each(data.areas, function(key, area) {
                let selected = (selectedAreaId && area.id == selectedAreaId) ? 'selected' : '';
                $(targetAreaSelect).append('<option value="' + area.id + '" ' + selected + '>' + area.name + '</option>');
              });
            }
            if (callback) callback();
          },
          error: function() {
            $(targetAreaSelect).html('<option value="">Select Area</option>');
          }
        });
      } else {
        $(targetAreaSelect).html('<option value="">Select Area</option>');
      }
    }

    // Function to update country and province from city selection
    function updateLocationFromCity(selectElement, countryField, provinceField) {
      const selectedOption = $(selectElement).find('option:selected');
      const countryName = selectedOption.data('country');
      const provinceName = selectedOption.data('province');
      
      $(countryField).val(countryName || '');
      $(provinceField).val(provinceName || '');
    }

    // Store original permanent values for "Same as Present" checkbox
    let originalPermanentAddress = $('#permanentAddressFields').html();
    let originalPermanentCity = $('#permanent_city_id').val();
    let originalPermanentArea = $('#permanent_area_id').val();
    let originalPermanentCountry = $('#permanent_country').val();
    let originalPermanentProvince = $('#permanent_province').val();

    // Present Address: When city changes, update country, province, and load areas
    $('#present_city_id').on('change', function() {
      const cityId = $(this).val();
      updateLocationFromCity(this, '#present_country', '#present_province');
      loadAreas(cityId, '#present_area_id', <?php echo $guardian['present_area_id'] ?: 'null'; ?>);
    });

    // Permanent Address: When city changes, update country, province, and load areas
    $('#permanent_city_id').on('change', function() {
      const cityId = $(this).val();
      updateLocationFromCity(this, '#permanent_country', '#permanent_province');
      loadAreas(cityId, '#permanent_area_id', <?php echo $guardian['permanent_area_id'] ?: 'null'; ?>);
    });

    // Same as Present checkbox functionality
    $('#sameAsPresent').on('change', function() {
      if ($(this).is(':checked')) {
        // Store current permanent values before overwriting
        originalPermanentAddress = $('#permanentAddressFields').html();
        originalPermanentCity = $('#permanent_city_id').val();
        originalPermanentArea = $('#permanent_area_id').val();
        originalPermanentCountry = $('#permanent_country').val();
        originalPermanentProvince = $('#permanent_province').val();
        
        // Copy values from present to permanent
        const presentAddress = $('textarea[name="present_address"]').val();
        const presentCity = $('#present_city_id').val();
        const presentArea = $('#present_area_id').val();
        
        $('textarea[name="permanent_address"]').val(presentAddress);
        
        // Set city (this will trigger change event to load areas)
        $('#permanent_city_id').val(presentCity).trigger('change');
        
        // Set area after areas are loaded
        setTimeout(function() {
          $('#permanent_area_id').val(presentArea);
        }, 300);
        
        // Set country and province manually
        const presentCountry = $('#present_country').val();
        const presentProvince = $('#present_province').val();
        $('#permanent_country').val(presentCountry);
        $('#permanent_province').val(presentProvince);
        
        // Disable permanent address fields
        $('#permanentAddressFields select, #permanentAddressFields textarea').prop('disabled', true);
        $('#permanent_country, #permanent_province').prop('disabled', true);
      } else {
        // Enable and restore original permanent address fields
        $('#permanentAddressFields select, #permanentAddressFields textarea').prop('disabled', false);
        $('#permanent_country, #permanent_province').prop('disabled', false);
        
        // Restore original values
        $('textarea[name="permanent_address"]').val(originalPermanentAddress);
        $('#permanent_city_id').val(originalPermanentCity).trigger('change');
        $('#permanent_country').val(originalPermanentCountry);
        $('#permanent_province').val(originalPermanentProvince);
        
        setTimeout(function() {
          $('#permanent_area_id').val(originalPermanentArea);
        }, 300);
      }
    });

    // Form submission
    $('#guardianEditForm').on('submit', function(e) {
      e.preventDefault();
      
      // Enable disabled fields before submit (if any)
      $('#permanentAddressFields select, #permanentAddressFields textarea').prop('disabled', false);
      $('#permanent_country, #permanent_province').prop('disabled', false);
      
      const formData = $(this).serialize();
      
      $.ajax({
        url: 'ajax/update_guardian.php',
        type: 'POST',
        data: formData,
        dataType: 'json',
        success: function(response) {
          if (response.success) {
            Swal.fire({
              icon: 'success',
              title: 'Success!',
              text: response.message,
              showConfirmButton: true,
              confirmButtonText: 'OK'
            }).then((result) => {
              if (result.isConfirmed) {
                window.location.href = 'guardian_list.php';
              }
            });
          } else {
            Swal.fire({
              icon: 'error',
              title: 'Error!',
              text: response.message
            });
          }
        },
        error: function() {
          Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: 'An error occurred. Please try again.'
          });
        }
      });
    });
  </script>
</body>
</html>