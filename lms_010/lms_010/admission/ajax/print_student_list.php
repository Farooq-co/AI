<?php
// print_student_list.php
include '../../connect.php';

// Fetch all students with related data (class, section, group, etc.)
$sql = "SELECT s.*, 
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
        ORDER BY s.id DESC";
$result = $conn->query($sql);

// Store all rows in array for CSV export
$studentsData = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $studentsData[] = $row;
    }
    // Reset result pointer for table display
    $result->data_seek(0);
}

// CSV Export handling
if (isset($_GET['export_csv'])) {
    // Set headers for CSV download
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="student_list_' . date('Y-m-d') . '.csv"');
    
    // Create output stream
    $output = fopen('php://output', 'w');
    
    // Add UTF-8 BOM for Excel compatibility
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
    
    // CSV Headers
    fputcsv($output, [
        'ID',
        'Student Name',
        'Father Name',
        'Date of Birth',
        'Admission Date',
        'Admission Number',
        'Roll Number',
        'Class',
        'Section',
        'Group',
        'Category',
        'Religion',
        'Gender',
        'Blood Group',
        'Family Number',
        'Hobbies',
        'Place of Birth',
        'Fee Package ID',
        'Guardian ID',
        'Status',
        'Created At'
    ]);
    
    // Write data rows
    foreach ($studentsData as $row) {
        // Format dates
        $dobFormatted = date('F d, Y', strtotime($row['date_of_birth']));
        $admissionDateFormatted = date('F d, Y', strtotime($row['admission_date']));
        $createdAtFormatted = date('F d, Y', strtotime($row['created_at']));
        
        fputcsv($output, [
            $row['id'],
            $row['student_name'],
            $row['father_name'],
            $dobFormatted,
            $admissionDateFormatted,
            $row['admission_number'] ?? '',
            $row['roll_number'] ?? '',
            $row['class_name'] ?? '',
            $row['section_name'] ?? '',
            $row['group_name'] ?? '',
            $row['category_name'] ?? '',
            $row['religion_name'] ?? '',
            $row['gender_name'] ?? '',
            $row['blood_group_name'] ?? '',
            $row['family_number'] ?? '',
            $row['hobbies'] ?? '',
            $row['place_of_birth'] ?? '',
            $row['fee_package_id'] ?? '',
            $row['guardian_id'] ?? '',
            $row['status'],
            $createdAtFormatted
        ]);
    }
    
    fclose($output);
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student List - Print</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: white;
            padding: 15px;
            font-size: 12px;
        }
        
        .print-container {
            max-width: 1400px;
            margin: 0 auto;
        }
        
        /* Header Section */
        .header {
            text-align: center;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #333;
        }
        
        .header h1 {
            font-size: 20px;
            margin-bottom: 5px;
        }
        
        .header p {
            color: #555;
            font-size: 11px;
        }
        
        .print-date {
            text-align: right;
            font-size: 10px;
            margin-bottom: 10px;
            color: #666;
        }
        
        /* Table Styles */
        .student-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
        }
        
        .student-table th,
        .student-table td {
            border: 1px solid #ccc;
            padding: 8px 6px;
            vertical-align: top;
        }
        
        .student-table th {
            background-color: #f2f2f2;
            font-weight: 600;
            text-align: center;
            font-size: 10px;
        }
        
        .student-table td {
            line-height: 1.3;
        }
        
        /* Sub-header rows for better readability */
        .sub-header {
            background-color: #f9f9f9;
            font-size: 9px;
            color: #666;
        }
        
        .badge-print {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
        }
        
        .badge-active {
            background-color: #28a745;
            color: white;
        }
        
        .badge-inactive {
            background-color: #dc3545;
            color: white;
        }
        
        .info-line {
            margin-bottom: 3px;
        }
        
        .compact-row {
            margin-bottom: 2px;
            line-height: 1.2;
        }
        
        /* Box-shaped photo styling using img tag */
        .photo-cell {
            text-align: center;
            vertical-align: middle;
        }
        
        .photo-box {
            width: 70px;
            height: 70px;
            border: 2px solid #ccc;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            background-color: #f0f0f0;
        }
        
        .photo-box img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .photo-placeholder {
            width: 70px;
            height: 70px;
            border: 2px solid #ccc;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            font-weight: bold;
            color: #999;
            background-color: #f0f0f0;
        }
        
        /* Ensure images print properly */
        @media print {
            body {
                padding: 5px;
                margin: 0;
            }
            
            .no-print {
                display: none;
            }
            
            .student-table th,
            .student-table td {
                border-color: #000;
            }
            
            .header {
                margin-bottom: 10px;
            }
            
            .photo-box {
                border: 1px solid #000;
                break-inside: avoid;
                page-break-inside: avoid;
            }
            
            .photo-box img {
                max-width: 100%;
                height: auto;
            }
            
            /* Force print background colors and images */
            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
                color-adjust: exact !important;
            }
        }
        
        /* Button styles */
        .btn {
            display: inline-block;
            padding: 8px 20px;
            margin-bottom: 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            text-decoration: none;
            transition: background-color 0.3s;
        }
        
        .print-btn {
            background-color: #4CAF50;
            color: white;
        }
        
        .print-btn:hover {
            background-color: #45a049;
        }
        
        .csv-btn {
            background-color: #2196F3;
            color: white;
            margin-right: 10px;
        }
        
        .csv-btn:hover {
            background-color: #0b7dda;
        }
        
        .back-btn {
            background-color: #6c757d;
            color: white;
            margin-right: 10px;
        }
        
        .back-btn:hover {
            background-color: #5a6268;
        }
        
        .button-group {
            text-align: right;
            margin-bottom: 10px;
        }
        
        .label {
            font-weight: 600;
            color: #555;
        }
        
        /* Father & DOB merged column */
        .father-info {
            line-height: 1.4;
        }
        .father-name {
            font-weight: 600;
            font-size: 11px;
        }
        .dob-text {
            font-size: 10px;
            color: #555;
            margin-top: 3px;
        }
    </style>
</head>
<body>
    <div class="print-container">
        <div class="no-print button-group">
            <a href="print_student_list.php" class="btn back-btn">↩ Back</a>
            <a href="?export_csv=1" class="btn csv-btn">📊 Download CSV</a>
            <button class="btn print-btn" onclick="window.print();">🖨️ Print / Save as PDF</button>
        </div>
        
        <div class="print-date">
            Generated on: <?php echo date('F d, Y h:i A'); ?>
        </div>
        
        <div class="header">
            <h1>Student Management List</h1>
            <p>Complete Student Records - Academic & Personal Information</p>
        </div>
        
        <table class="student-table">
            <thead>
                <tr>
                    <th width="30">ID</th>
                    <th width="80">Photo</th>
                    <th width="150">Student Information</th>
                    <th width="130">Academic Information</th>
                    <th width="120">Father & DOB</th>
                    <th width="90">Gender/Religion</th>
                    <th width="60">Status</th>
                    <th width="80">Created At</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (!$result) {
                    echo "<tr><td colspan='8' style='text-align:center; color:red;'>Error: " . $conn->error . "</td></tr>";
                } elseif ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        // Format dates
                        $createdAtFormatted = date('F d, Y', strtotime($row['created_at']));
                        $dobFormatted = date('d-m-Y', strtotime($row['date_of_birth']));
                        $admissionDateFormatted = date('d-m-Y', strtotime($row['admission_date']));
                        $admissionEffectiveFormatted = date('d-m-Y', strtotime($row['admission_effective_date']));
                        
                        // Student Information
                        $studentInfo = "<div class='info-line'><strong>" . htmlspecialchars($row['student_name']) . "</strong></div>";
                        $studentInfo .= "<div class='info-line'>Adm No: " . (htmlspecialchars($row['admission_number'] ?? 'N/A')) . "</div>";
                        $studentInfo .= "<div class='info-line'>Roll No: " . (htmlspecialchars($row['roll_number'] ?? 'N/A')) . "</div>";
                        $studentInfo .= "<div class='info-line'>Adm Date: " . $admissionDateFormatted . "</div>";
                        if(!empty($row['family_number'])) {
                            $studentInfo .= "<div class='info-line'>Family #: " . htmlspecialchars($row['family_number']) . "</div>";
                        }
                        if(!empty($row['hobbies'])) {
                            $studentInfo .= "<div class='info-line'>Hobbies: " . htmlspecialchars(substr($row['hobbies'], 0, 30)) . "</div>";
                        }
                        
                        // Academic Information
                        $academicInfo = "<div class='info-line'><strong>Class:</strong> " . htmlspecialchars($row['class_name'] ?? 'N/A') . "</div>";
                        $academicInfo .= "<div class='info-line'><strong>Section:</strong> " . htmlspecialchars($row['section_name'] ?? 'N/A') . "</div>";
                        $academicInfo .= "<div class='info-line'><strong>Group:</strong> " . htmlspecialchars($row['group_name'] ?? 'N/A') . "</div>";
                        $academicInfo .= "<div class='info-line'><strong>Category:</strong> " . htmlspecialchars($row['category_name'] ?? 'N/A') . "</div>";
                        if(!empty($row['fee_package_id'])) {
                            $academicInfo .= "<div class='info-line'><strong>Fee Pkg ID:</strong> " . $row['fee_package_id'] . "</div>";
                        }
                        if(!empty($row['admission_number'])) {
                            $academicInfo .= "<div class='info-line'><strong>Adm Effective:</strong> " . $admissionEffectiveFormatted . "</div>";
                        }
                        
                        // Father Name and DOB - Merged Column
                        $fatherDobInfo = "<div class='father-info'>";
                        $fatherDobInfo .= "<div class='father-name'>" . htmlspecialchars($row['father_name'] ?? 'N/A') . "</div>";
                        $fatherDobInfo .= "<div class='dob-text'>DOB: " . $dobFormatted . "</div>";
                        $fatherDobInfo .= "</div>";
                        
                        // Gender and Religion
                        $genderReligion = "<div class='info-line'>" . htmlspecialchars($row['gender_name'] ?? 'N/A') . "</div>";
                        $genderReligion .= "<div class='info-line'>" . htmlspecialchars($row['religion_name'] ?? 'N/A') . "</div>";
                        $genderReligion .= "<div class='info-line'><strong>Blood:</strong> " . htmlspecialchars($row['blood_group_name'] ?? 'N/A') . "</div>";
                        
                        // Student Photo - Using img tag for better print compatibility
                        $photoHtml = '';
                        $imagePath = '';
                        if (!empty($row['student_picture'])) {
                            // Check multiple possible paths
                            $possiblePaths = [
                                '../../uploads/students/' . $row['student_picture'],
                                '../uploads/students/' . $row['student_picture'],
                                'uploads/students/' . $row['student_picture']
                            ];
                            
                            $imageFound = false;
                            foreach ($possiblePaths as $path) {
                                if (file_exists($path)) {
                                    $imagePath = $path;
                                    $imageFound = true;
                                    break;
                                }
                            }
                            
                            if ($imageFound) {
                                $photoHtml = '<div class="photo-box"><img src="' . $imagePath . '" alt="Student Photo"></div>';
                            } else {
                                $photoHtml = '<div class="photo-placeholder">📷</div>';
                            }
                        } else {
                            $photoHtml = '<div class="photo-placeholder">📷</div>';
                        }
                        
                        $statusBadge = ($row["status"] == 'Active') 
                            ? "<span class='badge-print badge-active'>Active</span>" 
                            : "<span class='badge-print badge-inactive'>Inactive</span>";
                        
                        echo "<tr>";
                        echo "<td style='text-align:center;'>" . $row["id"] . "</td>";
                        echo "<td class='photo-cell' style='text-align:center;'>" . $photoHtml . "</td>";
                        echo "<td>" . $studentInfo . "</td>";
                        echo "<td>" . $academicInfo . "</td>";
                        echo "<td>" . $fatherDobInfo . "</td>";
                        echo "<td>" . $genderReligion . "</td>";
                        echo "<td style='text-align:center;'>" . $statusBadge . "</td>";
                        echo "<td style='text-align:center;'>" . $createdAtFormatted . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='8' style='text-align:center;'>No student records found</td></tr>";
                }
                
                $conn->close();
                ?>
            </tbody>
        </table>
        
        <div class="footer">
            <p>This is a system-generated report. Total students: <?php echo count($studentsData); ?></p>
        </div>
    </div>
    
    <script>
        // Auto-trigger print dialog when page loads (optional)
        // Uncomment the line below if you want print dialog to open automatically
        // window.onload = function() { window.print(); };
    </script>
</body>
</html>