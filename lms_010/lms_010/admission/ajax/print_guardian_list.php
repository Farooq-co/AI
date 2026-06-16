<?php
// print_guardian_list.php
include '../../connect.php';

// Fetch all guardians with city and area names, ordered by ID
$sql = "SELECT sg.*, 
        pc.name as present_city_name, 
        pa.name as present_area_name,
        perc.name as permanent_city_name,
        pera.name as permanent_area_name
        FROM student_guardians sg 
        LEFT JOIN cities pc ON sg.present_city_id = pc.id 
        LEFT JOIN areas pa ON sg.present_area_id = pa.id
        LEFT JOIN cities perc ON sg.permanent_city_id = perc.id 
        LEFT JOIN areas pera ON sg.permanent_area_id = pera.id
        ORDER BY sg.id DESC";
$result = $conn->query($sql);

// Store all rows in array for CSV export
$guardiansData = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $guardiansData[] = $row;
    }
    // Reset result pointer for table display
    $result->data_seek(0);
}

// CSV Export handling
if (isset($_GET['export_csv'])) {
    // Set headers for CSV download
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="guardian_list_' . date('Y-m-d') . '.csv"');
    
    // Create output stream
    $output = fopen('php://output', 'w');
    
    // Add UTF-8 BOM for Excel compatibility
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
    
    // CSV Headers
    fputcsv($output, [
        'ID',
        'Father Name',
        'Father CNIC',
        'Father Mobile',
        'Father WhatsApp',
        'Father Profession',
        'Mother Name',
        'Mother CNIC',
        'Mother Mobile',
        'Mother WhatsApp',
        'Mother Profession',
        'Guardian Name',
        'Guardian CNIC',
        'Guardian Mobile',
        'Guardian WhatsApp',
        'Guardian Profession',
        'Present Address',
        'Present City',
        'Present Area',
        'Present Country',
        'Permanent Address',
        'Permanent City',
        'Permanent Area',
        'Permanent Country',
        'Status',
        'Created At'
    ]);
    
    // Write data rows
    foreach ($guardiansData as $row) {
        // Format created_at date
        $createdAtFormatted = date('F d, Y', strtotime($row['created_at']));
        
        fputcsv($output, [
            $row['id'],
            $row['father_name'],
            $row['father_cnic'] ?? '',
            $row['father_mobile'] ?? '',
            $row['father_whatsapp_number'] ?? '',
            $row['father_profession'] ?? '',
            $row['mother_name'],
            $row['mother_cnic'] ?? '',
            $row['mother_mobile'] ?? '',
            $row['mother_whatsapp_number'] ?? '',
            $row['mother_profession'] ?? '',
            $row['guardian_name'],
            $row['guardian_cnic'] ?? '',
            $row['guardian_mobile'] ?? '',
            $row['guardian_whatsapp_number'] ?? '',
            $row['guardian_profession'] ?? '',
            $row['present_address'],
            $row['present_city_name'] ?? '',
            $row['present_area_name'] ?? '',
            $row['present_country'] ?? '',
            $row['permanent_address'] ?? '',
            $row['permanent_city_name'] ?? '',
            $row['permanent_area_name'] ?? '',
            $row['permanent_country'] ?? '',
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
    <title>Guardian List - Print</title>
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
        .guardian-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
        }
        
        .guardian-table th,
        .guardian-table td {
            border: 1px solid #ccc;
            padding: 6px 4px;
            vertical-align: top;
        }
        
        .guardian-table th {
            background-color: #f2f2f2;
            font-weight: 600;
            text-align: center;
            font-size: 10px;
        }
        
        .guardian-table td {
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
        
        .address-block {
            font-size: 9px;
            line-height: 1.3;
        }
        
        .info-line {
            margin-bottom: 2px;
        }
        
        .compact-row {
            margin-bottom: 2px;
            line-height: 1.2;
        }
        
        /* Footer */
        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 9px;
            color: #777;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        
        /* Print-specific styles */
        @media print {
            body {
                padding: 5px;
                margin: 0;
            }
            
            .no-print {
                display: none;
            }
            
            .guardian-table th,
            .guardian-table td {
                border-color: #000;
            }
            
            .header {
                margin-bottom: 10px;
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
        
        .button-group {
            text-align: right;
            margin-bottom: 10px;
        }
        
        .label {
            font-weight: 600;
            color: #555;
        }
    </style>
</head>
<body>
    <div class="print-container">
        <div class="no-print button-group">
            <a href="?export_csv=1" class="btn csv-btn">📊 Download CSV</a>
            <button class="btn print-btn" onclick="window.print();">🖨️ Print / Save as PDF</button>
        </div>
        
        <div class="print-date">
            Generated on: <?php echo date('F d, Y h:i A'); ?>
        </div>
        
        <div class="header">
            <h1>Guardian Management List</h1>
            <p>Complete Guardian Records - Father, Mother & Guardian Information</p>
        </div>
        
        <table class="guardian-table">
            <thead>
                <tr>
                    <th width="30">ID</th>
                    <th width="150">Father Information</th>
                    <th width="150">Mother Information</th>
                    <th width="150">Guardian Information</th>
                    <th width="180">Present Address</th>
                    <th width="180">Permanent Address</th>
                    <th width="60">Status</th>
                    <th width="80">Created At</th>
                </tr>
                <tr class="sub-header">
                    <th></th>
                    <th>Name / CNIC / Contact</th>
                    <th>Name / CNIC / Contact</th>
                    <th>Name / CNIC / Contact</th>
                    <th>Address Detail</th>
                    <th>Address Detail</th>
                    <th></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (!$result) {
                    echo "<tr><td colspan='8' style='text-align:center; color:red;'>Error: " . $conn->error . "</td></tr>";
                } elseif ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        // Format created_at date as "April 08, 2026"
                        $createdAtFormatted = date('F d, Y', strtotime($row['created_at']));
                        
                        // Father Information
                        $fatherInfo = "<div class='info-line'><strong>" . htmlspecialchars($row['father_name']) . "</strong></div>";
                        $fatherInfo .= "<div class='info-line'>" . (htmlspecialchars($row['father_cnic'] ?? 'N/A')) . "</div>";
                        $fatherInfo .= "<div class='info-line'>" . (htmlspecialchars($row['father_mobile'] ?? 'N/A')) . "</div>";
                        if(!empty($row['father_whatsapp_number'])) {
                            $fatherInfo .= "<div class='info-line'>" . htmlspecialchars($row['father_whatsapp_number']) . "</div>";
                        }
                        if(!empty($row['father_profession'])) {
                            $fatherInfo .= "<div class='info-line'>" . htmlspecialchars($row['father_profession']) . "</div>";
                        }
                        
                        // Mother Information
                        $motherInfo = "<div class='info-line'><strong>" . htmlspecialchars($row['mother_name']) . "</strong></div>";
                        $motherInfo .= "<div class='info-line'>" . (htmlspecialchars($row['mother_cnic'] ?? 'N/A')) . "</div>";
                        $motherInfo .= "<div class='info-line'>" . (htmlspecialchars($row['mother_mobile'] ?? 'N/A')) . "</div>";
                        if(!empty($row['mother_whatsapp_number'])) {
                            $motherInfo .= "<div class='info-line'>" . htmlspecialchars($row['mother_whatsapp_number']) . "</div>";
                        }
                        if(!empty($row['mother_profession'])) {
                            $motherInfo .= "<div class='info-line'>" . htmlspecialchars($row['mother_profession']) . "</div>";
                        }
                        
                        // Guardian Information
                        $guardianInfo = "<div class='info-line'><strong>" . htmlspecialchars($row['guardian_name']) . "</strong></div>";
                        $guardianInfo .= "<div class='info-line'>" . (htmlspecialchars($row['guardian_cnic'] ?? 'N/A')) . "</div>";
                        $guardianInfo .= "<div class='info-line'>" . (htmlspecialchars($row['guardian_mobile'] ?? 'N/A')) . "</div>";
                        if(!empty($row['guardian_whatsapp_number'])) {
                            $guardianInfo .= "<div class='info-line'>" . htmlspecialchars($row['guardian_whatsapp_number']) . "</div>";
                        }
                        if(!empty($row['guardian_profession'])) {
                            $guardianInfo .= "<div class='info-line'>" . htmlspecialchars($row['guardian_profession']) . "</div>";
                        }
                        
                        // Present Address - City/Area/Country in same row
                        $presentAddress = "<div class='address-block'>" . nl2br(htmlspecialchars($row['present_address'])) . "</div>";
                        
                        // Build location string in same row
                        $locationParts = array();
                        if(!empty($row['present_city_name'])) $locationParts[] = "<span class='label'></span> " . htmlspecialchars($row['present_city_name']);
                        if(!empty($row['present_area_name'])) $locationParts[] = "<span class='label'></span> " . htmlspecialchars($row['present_area_name']);
                        if(!empty($row['present_country'])) $locationParts[] = "<span class='label'></span> " . htmlspecialchars($row['present_country']);
                        
                        if(!empty($locationParts)) {
                            $presentAddress .= "<div class='compact-row'>" . implode(", ", $locationParts) . "</div>";
                        }
                        
                        // Permanent Address - City/Area/Country in same row
                        if(!empty($row['permanent_address'])) {
                            $permanentAddress = "<div class='address-block'>" . nl2br(htmlspecialchars($row['permanent_address'])) . "</div>";
                            
                            $permLocationParts = array();
                            if(!empty($row['permanent_city_name'])) $permLocationParts[] = "<span class='label'></span> " . htmlspecialchars($row['permanent_city_name']);
                            if(!empty($row['permanent_area_name'])) $permLocationParts[] = "<span class='label'></span> " . htmlspecialchars($row['permanent_area_name']);
                            if(!empty($row['permanent_country'])) $permLocationParts[] = "<span class='label'></span> " . htmlspecialchars($row['permanent_country']);
                            
                            if(!empty($permLocationParts)) {
                                $permanentAddress .= "<div class='compact-row'>" . implode(", ", $permLocationParts) . "</div>";
                            }
                        } else {
                            $permanentAddress = "<em>Same as Present Address</em>";
                        }
                        
                        $statusBadge = ($row["status"] == 'Active') 
                            ? "<span class='badge-print badge-active'>Active</span>" 
                            : "<span class='badge-print badge-inactive'>Inactive</span>";
                        
                        echo "<tr>";
                        echo "<td style='text-align:center;'>" . $row["id"] . "</td>";
                        echo "<td>" . $fatherInfo . "</td>";
                        echo "<td>" . $motherInfo . "</td>";
                        echo "<td>" . $guardianInfo . "</td>";
                        echo "<td>" . $presentAddress . "</td>";
                        echo "<td>" . $permanentAddress . "</td>";
                        echo "<td style='text-align:center;'>" . $statusBadge . "</td>";
                        echo "<td style='text-align:center;'>" . $createdAtFormatted . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='8' style='text-align:center;'>No guardian records found</td></tr>";
                }
                
                $conn->close();
                ?>
            </tbody>
        </table>
        
        <div class="footer">
            <p>This is a system-generated report. Total records: <?php echo count($guardiansData); ?></p>
        </div>
    </div>
    
    <script>
        // Auto-trigger print dialog when page loads (optional)
        // Uncomment the line below if you want print dialog to open automatically
        // window.onload = function() { window.print(); };
    </script>
</body>
</html>