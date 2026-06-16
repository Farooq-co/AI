<!DOCTYPE html>
<html lang="en">
<head>
    <title>Student Admission Report</title>
    <?php include '../parts/links1.php'; ?>
    <style>
        /* Reset for clean report */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            background: white;
            font-family: 'Segoe UI', 'Roboto', Arial, sans-serif;
            font-size: 13px;
            line-height: 1.35;
            color: #000;
        }
        
        /* Print Styles - Black & White Optimized */
        @media print {
            body {
                margin: 0;
                padding: 0;
            }
            .no-print {
                display: none !important;
            }
            .report-container {
                margin: 0;
                padding: 0.15in;
                width: 100%;
            }
            @page {
                size: A4;
                margin: 0.2in;
            }
            .page-break {
                page-break-inside: avoid;
            }
            .border-bottom {
                border-bottom: 1px solid #000 !important;
            }
            .border-right {
                border-right: 1px solid #000 !important;
            }
            .border-left {
                border-left: 1px solid #000 !important;
            }
            .border-top {
                border-top: 1px solid #000 !important;
            }
        }
        
        /* Screen Styles - Minimalist B&W */
        .no-print {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 1000;
        }
        .no-print .print-btn {
            padding: 10px 20px;
            font-size: 14px;
            cursor: pointer;
            background: #4e73df;
            border: none;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }
        .no-print .print-btn:hover {
            background: #2e59d9;
        }
        
        .report-container {
            max-width: 1100px;
            margin: 0 auto;
            background: white;
            padding: 5px;
        }
        
        /* Report Header */
        .report-header {
            text-align: center;
            margin-bottom: 12px;
            border-bottom: 2px solid #000;
            padding-bottom: 8px;
        }
        .report-title {
            font-size: 20px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .report-subtitle {
            font-size: 13px;
            margin-top: 3px;
        }
        .report-id {
            font-size: 11px;
            margin-top: 4px;
        }
        
        /* Student Photo Section */
        .photo-section {
            float: right;
            width: 110px;
            text-align: center;
            margin-left: 15px;
            margin-bottom: 8px;
            border: 1px solid #000;
            padding: 5px;
        }
        .student-photo {
            width: 98px;
            height: 110px;
            object-fit: cover;
            background: #f5f5f5;
        }
        .photo-placeholder {
            width: 98px;
            height: 110px;
            background: #f5f5f5;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 45px;
            color: #999;
        }
        .photo-label {
            font-size: 9px;
            margin-top: 3px;
            font-weight: bold;
        }
        
        /* Info Table Styles */
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        .info-table td, .info-table th {
            border: 1px solid #000;
            padding: 7px 10px;
            vertical-align: top;
        }
        .info-table th {
            background: #f5f5f5;
            font-weight: bold;
            text-align: center;
            font-size: 12px;
        }
        .label-cell {
            background: #f9f9f9;
            font-weight: bold;
            width: 140px;
        }
        
        /* Section Headers */
        .section-header {
            background: #e0e0e0;
            font-weight: bold;
            font-size: 15px;
            padding: 5px 10px;
            margin-top: 8px;
            margin-bottom: 6px;
            border: 1px solid #000;
            border-bottom: 2px solid #000;
        }
        .section-header i {
            margin-right: 5px;
        }
        
        /* Guardian Cards */
        .guardian-row {
            display: flex;
            gap: 10px;
            margin-bottom: 10px;
        }
        .guardian-col {
            flex: 1;
            border: 1px solid #000;
        }
        .guardian-title {
            background: #e0e0e0;
            font-weight: bold;
            padding: 6px 8px;
            border-bottom: 1px solid #000;
            font-size: 13px;
        }
        .guardian-body {
            padding: 6px 8px;
        }
        .guardian-detail {
            margin-bottom: 5px;
            font-size: 12px;
        }
        .guardian-detail strong {
            display: inline-block;
            width: 85px;
        }
        
        /* Address Section - Increased Font Size */
        .address-row {
            display: flex;
            gap: 10px;
            margin-bottom: 10px;
        }
        .address-col {
            flex: 1;
            border: 1px solid #000;
        }
        .address-title {
            background: #e0e0e0;
            font-weight: bold;
            padding: 6px 10px;
            border-bottom: 1px solid #000;
            font-size: 13px;
        }
        .address-body {
            padding: 8px 10px;
            font-size: 13px;
            line-height: 1.5;
        }
        .address-text {
            margin-bottom: 8px;
            font-size: 13px;
        }
        .address-line {
            font-size: 13px;
            margin-top: 5px;
        }
        
        /* Status Badge B&W */
        .status-active {
            font-weight: bold;
            color: #000;
            background: #fff;
            border: 1px solid #000;
            padding: 2px 8px;
            display: inline-block;
        }
        .status-inactive {
            font-weight: bold;
            color: #000;
            background: #ddd;
            border: 1px solid #000;
            padding: 2px 8px;
            display: inline-block;
        }
        
        /* Signature Section - Increased spacing */
        .signature-row {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px dashed #000;
        }
        .signature-item {
            text-align: center;
            width: 30%;
        }
        .signature-line {
            border-top: 1px solid #000;
            width: 100%;
            margin-bottom: 8px;
            margin-top: 45px;
        }
        .signature-item div:first-child {
            margin-bottom: 8px;
        }
        
        /* Footer */
        .report-footer {
            text-align: center;
            font-size: 9px;
            margin-top: 12px;
            padding-top: 6px;
            border-top: 1px solid #ccc;
        }
        
        /* Clearfix */
        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }
        
        @media (max-width: 700px) {
            .guardian-row, .address-row {
                flex-direction: column;
            }
            .label-cell {
                width: 110px;
            }
        }
    </style>
</head>
<body>

<!-- Only Print Button - Floating at bottom right -->
<div class="no-print">
    <button onclick="window.print();" class="print-btn">
        <i class="bi bi-printer"></i> Print / Save as PDF
    </button>
</div>

<?php
// Get student ID from URL
$studentId = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($studentId <= 0) {
    echo '<div style="padding: 20px; text-align: center;">Invalid student ID</div>';
    exit;
}

include '../connect.php';

// Function to format date to "Month Day, Year"
function formatDate($date) {
    if (empty($date) || $date == '0000-00-00') {
        return 'N/A';
    }
    return date('F j, Y', strtotime($date));
}

// Function to format address components in one line
function formatAddressLine($city, $area, $province, $country) {
    $parts = array();
    if (!empty($city) && $city != 'N/A') $parts[] = $city;
    if (!empty($area) && $area != 'N/A') $parts[] = $area;
    if (!empty($province) && $province != 'N/A') $parts[] = $province;
    if (!empty($country) && $country != 'N/A') $parts[] = $country;
    return !empty($parts) ? implode(', ', $parts) : 'N/A';
}

// Fetch student data
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
    echo '<div style="padding: 20px; text-align: center;">Student not found</div>';
    exit;
}

$student = $studentResult->fetch_assoc();

// Fetch guardian data
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
        } else {
            $guardianData['present_city_name'] = 'N/A';
        }
        
        if ($guardianData['permanent_city_id']) {
            $cityQuery = "SELECT name FROM cities WHERE id = " . intval($guardianData['permanent_city_id']);
            $cityResult = $conn->query($cityQuery);
            if ($cityResult && $cityResult->num_rows > 0) {
                $city = $cityResult->fetch_assoc();
                $guardianData['permanent_city_name'] = $city['name'];
            }
        } else {
            $guardianData['permanent_city_name'] = 'N/A';
        }
        
        if ($guardianData['present_area_id']) {
            $areaQuery = "SELECT name FROM areas WHERE id = " . intval($guardianData['present_area_id']);
            $areaResult = $conn->query($areaQuery);
            if ($areaResult && $areaResult->num_rows > 0) {
                $area = $areaResult->fetch_assoc();
                $guardianData['present_area_name'] = $area['name'];
            }
        } else {
            $guardianData['present_area_name'] = 'N/A';
        }
        
        if ($guardianData['permanent_area_id']) {
            $areaQuery = "SELECT name FROM areas WHERE id = " . intval($guardianData['permanent_area_id']);
            $areaResult = $conn->query($areaQuery);
            if ($areaResult && $areaResult->num_rows > 0) {
                $area = $areaResult->fetch_assoc();
                $guardianData['permanent_area_name'] = $area['name'];
            }
        } else {
            $guardianData['permanent_area_name'] = 'N/A';
        }
        
        // Set default values if null
        $guardianData['present_province'] = $guardianData['present_province'] ?? 'N/A';
        $guardianData['present_country'] = $guardianData['present_country'] ?? 'N/A';
        $guardianData['permanent_province'] = $guardianData['permanent_province'] ?? 'N/A';
        $guardianData['permanent_country'] = $guardianData['permanent_country'] ?? 'N/A';
    }
}

// Format dates
$admissionDate = formatDate($student['admission_date']);
$admissionEffectiveDate = formatDate($student['admission_effective_date']);
$dateOfBirth = formatDate($student['date_of_birth']);
$createdAt = formatDate($student['created_at']);
$updatedAt = !empty($student['updated_at']) ? formatDate($student['updated_at']) : $createdAt;
?>

<div class="report-container">
    
    <!-- Report Header -->
    <div class="report-header">
        <div class="report-title">Student Admission Report</div>
        <div class="report-subtitle">Official Student Record Document</div>
        <div class="report-id">Registration No: <?php echo str_pad($student['id'], 6, '0', STR_PAD_LEFT); ?> | Issue Date: <?php echo date('F j, Y'); ?></div>
    </div>
    
    <!-- Student Photo & Basic Info Row -->
    <div class="clearfix">
        <div class="photo-section">
            <?php if (!empty($student['student_picture']) && file_exists('../uploads/students/' . $student['student_picture'])): ?>
                <img src="../uploads/students/<?php echo htmlspecialchars($student['student_picture']); ?>" class="student-photo" alt="Student Photo">
            <?php else: ?>
                <div class="photo-placeholder">
                    <i class="bi bi-person" style="font-size: 45px;"></i>
                </div>
            <?php endif; ?>
            <div class="photo-label">Student ID: <?php echo $student['id']; ?></div>
        </div>
        
        <!-- Basic Info Table -->
        <table class="info-table" style="width: calc(100% - 130px);">

            <tr>
                <td class="label-cell">Student Name</td>
                <td style="width: 35%;"><strong><?php echo strtoupper(htmlspecialchars($student['student_name'])); ?></strong></td>
                <td class="label-cell" style="width: 100px;">Father's Name</td>
                <td><?php echo htmlspecialchars($student['father_name']); ?></td>
            </tr>
            <tr>
                <td class="label-cell">Date of Birth</td>
                <td><?php echo $dateOfBirth; ?></td>
                <td class="label-cell">Gender</td>
                <td><?php echo htmlspecialchars($student['gender_name'] ?? 'N/A'); ?></td>
            </tr>
            <tr>
                <td class="label-cell">Place of Birth</td>
                <td><?php echo !empty($student['place_of_birth']) ? htmlspecialchars($student['place_of_birth']) : 'N/A'; ?></td>
                <td class="label-cell">Blood Group</td>
                <td><?php echo htmlspecialchars($student['blood_group_name'] ?? 'N/A'); ?></td>
            </tr>
            <tr>
                <td class="label-cell">Religion</td>
                <td><?php echo htmlspecialchars($student['religion_name'] ?? 'N/A'); ?></td>
                <td class="label-cell">Category</td>
                <td><?php echo htmlspecialchars($student['category_name'] ?? 'N/A'); ?></td>
            </tr>
            <tr>
                <td class="label-cell">Hobbies</td>
                <td><?php echo !empty($student['hobbies']) ? htmlspecialchars($student['hobbies']) : 'N/A'; ?></td>
                <td class="label-cell">Status</td>
                <td>
                    <?php if ($student['status'] == 'Active'): ?>
                        <span class="status-active">ACTIVE</span>
                    <?php else: ?>
                        <span class="status-inactive">INACTIVE</span>
                    <?php endif; ?>
                </td>
            </tr>
        </table>
    </div>
    
    <!-- Academic Information Section -->
    <div class="section-header">
        <i class="bi bi-mortarboard"></i> ACADEMIC INFORMATION
    </div>
    <table class="info-table">
        <tr>
            <td class="label-cell" style="width: 140px;">Class</td>
            <td style="width: 35%;"><strong><?php echo htmlspecialchars($student['class_name'] ?? 'N/A'); ?></strong></td>
            <td class="label-cell" style="width: 100px;">Section</td>
            <td><?php echo htmlspecialchars($student['section_name'] ?? 'N/A'); ?></td>
        </tr>
        <tr>
            <td class="label-cell">Group</td>
            <td><?php echo htmlspecialchars($student['group_name'] ?? 'N/A'); ?></td>
            <td class="label-cell">Roll Number</td>
            <td><?php echo !empty($student['roll_number']) ? htmlspecialchars($student['roll_number']) : 'N/A'; ?></td>
        </tr>
        <tr>
            <td class="label-cell">Admission Number</td>
            <td><?php echo !empty($student['admission_number']) ? htmlspecialchars($student['admission_number']) : 'N/A'; ?></td>
            <td class="label-cell">Fee Package</td>
            <td><?php echo !empty($student['fee_package_id']) ? 'Package #' . $student['fee_package_id'] : 'N/A'; ?></td>
        </tr>
        <tr>
            <td class="label-cell">Admission Date</td>
            <td><?php echo $admissionDate; ?></td>
            <td class="label-cell">Effective Date</td>
            <td><?php echo $admissionEffectiveDate; ?></td>
        </tr>
        <tr>
            <td class="label-cell">Family Number</td>
            <td colspan="3"><?php echo !empty($student['family_number']) ? htmlspecialchars($student['family_number']) : 'N/A'; ?></td>
        </tr>
        <tr>
            <td class="label-cell">Search Fields</td>
            <td colspan="3">
                <?php 
                $searches = [];
                if(!empty($student['student_search1'])) $searches[] = $student['student_search1'];
                if(!empty($student['student_search2'])) $searches[] = $student['student_search2'];
                if(!empty($student['student_search3'])) $searches[] = $student['student_search3'];
                echo !empty($searches) ? implode(' | ', array_map('htmlspecialchars', $searches)) : 'N/A';
                ?>
            </td>
        </tr>
    </table>
    
    <!-- Guardian Information Section -->
    <?php if ($guardianData): ?>
    <div class="section-header">
        <i class="bi bi-people"></i> GUARDIAN INFORMATION
    </div>
    
    <div class="guardian-row">
        <!-- Father Information -->
        <?php if (!empty($guardianData['father_name'])): ?>
        <div class="guardian-col">
            <div class="guardian-title"><i class="bi bi-person-badge"></i> FATHER'S DETAILS</div>
            <div class="guardian-body">
                <div class="guardian-detail"><strong>Full Name:</strong> <?php echo htmlspecialchars($guardianData['father_name']); ?></div>
                <div class="guardian-detail"><strong>CNIC:</strong> <?php echo !empty($guardianData['father_cnic']) ? htmlspecialchars($guardianData['father_cnic']) : 'N/A'; ?></div>
                <div class="guardian-detail"><strong>Mobile:</strong> <?php echo !empty($guardianData['father_mobile']) ? htmlspecialchars($guardianData['father_mobile']) : 'N/A'; ?></div>
                <div class="guardian-detail"><strong>WhatsApp:</strong> <?php echo !empty($guardianData['father_whatsapp_number']) ? htmlspecialchars($guardianData['father_whatsapp_number']) : 'N/A'; ?></div>
                <div class="guardian-detail"><strong>Email:</strong> <?php echo !empty($guardianData['father_email']) ? htmlspecialchars($guardianData['father_email']) : 'N/A'; ?></div>
                <div class="guardian-detail"><strong>Profession:</strong> <?php echo !empty($guardianData['father_profession']) ? htmlspecialchars($guardianData['father_profession']) : 'N/A'; ?></div>
                <div class="guardian-detail"><strong>Education:</strong> <?php echo !empty($guardianData['father_education']) ? htmlspecialchars($guardianData['father_education']) : 'N/A'; ?></div>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Mother Information -->
        <?php if (!empty($guardianData['mother_name'])): ?>
        <div class="guardian-col">
            <div class="guardian-title"><i class="bi bi-person"></i> MOTHER'S DETAILS</div>
            <div class="guardian-body">
                <div class="guardian-detail"><strong>Full Name:</strong> <?php echo htmlspecialchars($guardianData['mother_name']); ?></div>
                <div class="guardian-detail"><strong>CNIC:</strong> <?php echo !empty($guardianData['mother_cnic']) ? htmlspecialchars($guardianData['mother_cnic']) : 'N/A'; ?></div>
                <div class="guardian-detail"><strong>Mobile:</strong> <?php echo !empty($guardianData['mother_mobile']) ? htmlspecialchars($guardianData['mother_mobile']) : 'N/A'; ?></div>
                <div class="guardian-detail"><strong>WhatsApp:</strong> <?php echo !empty($guardianData['mother_whatsapp_number']) ? htmlspecialchars($guardianData['mother_whatsapp_number']) : 'N/A'; ?></div>
                <div class="guardian-detail"><strong>Email:</strong> <?php echo !empty($guardianData['mother_email']) ? htmlspecialchars($guardianData['mother_email']) : 'N/A'; ?></div>
                <div class="guardian-detail"><strong>Profession:</strong> <?php echo !empty($guardianData['mother_profession']) ? htmlspecialchars($guardianData['mother_profession']) : 'N/A'; ?></div>
                <div class="guardian-detail"><strong>Education:</strong> <?php echo !empty($guardianData['mother_education']) ? htmlspecialchars($guardianData['mother_education']) : 'N/A'; ?></div>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Guardian Information -->
        <?php if (!empty($guardianData['guardian_name'])): ?>
        <div class="guardian-col">
            <div class="guardian-title"><i class="bi bi-shield"></i> GUARDIAN'S DETAILS</div>
            <div class="guardian-body">
                <div class="guardian-detail"><strong>Full Name:</strong> <?php echo htmlspecialchars($guardianData['guardian_name']); ?></div>
                <div class="guardian-detail"><strong>CNIC:</strong> <?php echo !empty($guardianData['guardian_cnic']) ? htmlspecialchars($guardianData['guardian_cnic']) : 'N/A'; ?></div>
                <div class="guardian-detail"><strong>Mobile:</strong> <?php echo !empty($guardianData['guardian_mobile']) ? htmlspecialchars($guardianData['guardian_mobile']) : 'N/A'; ?></div>
                <div class="guardian-detail"><strong>WhatsApp:</strong> <?php echo !empty($guardianData['guardian_whatsapp_number']) ? htmlspecialchars($guardianData['guardian_whatsapp_number']) : 'N/A'; ?></div>
                <div class="guardian-detail"><strong>Email:</strong> <?php echo !empty($guardianData['guardian_email']) ? htmlspecialchars($guardianData['guardian_email']) : 'N/A'; ?></div>
                <div class="guardian-detail"><strong>Profession:</strong> <?php echo !empty($guardianData['guardian_profession']) ? htmlspecialchars($guardianData['guardian_profession']) : 'N/A'; ?></div>
                <div class="guardian-detail"><strong>Education:</strong> <?php echo !empty($guardianData['guardian_education']) ? htmlspecialchars($guardianData['guardian_education']) : 'N/A'; ?></div>
            </div>
        </div>
        <?php endif; ?>
    </div>
    
    <!-- Address Information - Increased Font Size & Combined Line -->
    <div class="address-row">
        <div class="address-col">
            <div class="address-title"><i class="bi bi-house-door"></i> PRESENT ADDRESS</div>
            <div class="address-body">
                <div class="address-text">
                    <?php echo !empty($guardianData['present_address']) ? nl2br(htmlspecialchars($guardianData['present_address'])) : 'N/A'; ?>
                </div>
                <div class="address-line">
                    <strong>Location:</strong> <?php echo formatAddressLine(
                        $guardianData['present_city_name'] ?? 'N/A',
                        $guardianData['present_area_name'] ?? 'N/A',
                        $guardianData['present_province'] ?? 'N/A',
                        $guardianData['present_country'] ?? 'N/A'
                    ); ?>
                </div>
            </div>
        </div>
        <div class="address-col">
            <div class="address-title"><i class="bi bi-building"></i> PERMANENT ADDRESS</div>
            <div class="address-body">
                <div class="address-text">
                    <?php echo !empty($guardianData['permanent_address']) ? nl2br(htmlspecialchars($guardianData['permanent_address'])) : 'N/A'; ?>
                </div>
                <div class="address-line">
                    <strong>Location:</strong> <?php echo formatAddressLine(
                        $guardianData['permanent_city_name'] ?? 'N/A',
                        $guardianData['permanent_area_name'] ?? 'N/A',
                        $guardianData['permanent_province'] ?? 'N/A',
                        $guardianData['permanent_country'] ?? 'N/A'
                    ); ?>
                </div>
            </div>
        </div>
    </div>
    <?php else: ?>
    <div class="section-header">
        <i class="bi bi-people"></i> GUARDIAN INFORMATION
    </div>
    <table class="info-table">
        <tr>
            <td class="label-cell">Father's Name</td>
            <td><?php echo htmlspecialchars($student['father_name']); ?></td>
        </tr>
        <tr>
            <td class="label-cell">Note</td>
            <td><em>Complete guardian details not available in the system.</em></td>
        </tr>
    </table>
    <?php endif; ?>
    
    <!-- Record Information -->
    <div class="section-header">
        <i class="bi bi-clock-history"></i> RECORD INFORMATION
    </div>
    <table class="info-table">
        <tr>
            <td class="label-cell" style="width: 150px;">Created Date</td>
            <td style="width: 35%;"><?php echo $createdAt; ?></td>
            <td class="label-cell" style="width: 150px;">Last Updated</td>
            <td><?php echo $updatedAt; ?></td>
        </tr>
    </table>
    
    <!-- Signature Section - Increased spacing above signature line -->
    <div class="signature-row">
        <div class="signature-item">
            <div class="signature-line"></div>
            <div>Student's Signature</div>
        </div>
        <div class="signature-item">
            <div class="signature-line"></div>
            <div>Parent/Guardian Signature</div>
        </div>
        <div class="signature-item">
            <div class="signature-line"></div>
            <div>Principal/Authorized Signatory</div>
        </div>
    </div>
    
    <!-- Report Footer -->
    <div class="report-footer">
        This is a computer-generated document. No signature is required.<br>
        Generated on: <?php echo date('F j, Y h:i A'); ?> | System ID: <?php echo $student['id']; ?>
    </div>
    
</div>

<?php $conn->close(); ?>
</body>
</html>