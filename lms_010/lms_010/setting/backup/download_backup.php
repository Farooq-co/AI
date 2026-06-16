<?php
// Include the database connection
include '../../connect.php';
global $conn; // Ensure $conn is in the global scope

// Full path to mysqldump (adjust this path as needed)
$backup_file = $database . "_backup_" . date("Y-m-d_H-i-s") . ".sql";
$handle = fopen($backup_file, 'w+');

$tables = $conn->query('SHOW TABLES');
while ($row = $tables->fetch_row()) {
    $table = $row[0];

    // Drop table if exists
    $table_drop = "DROP TABLE IF EXISTS `$table`;\n";
    fwrite($handle, $table_drop);

    // Create table structure
    $create_table = $conn->query("SHOW CREATE TABLE `$table`")->fetch_row()[1] . ";\n\n";
    fwrite($handle, $create_table);

    // Insert data
    $rows = $conn->query("SELECT * FROM `$table`");
    while ($data = $rows->fetch_assoc()) {
        $columns = array_keys($data);
        $values  = array_values($data);

        $values = array_map(function ($value) use ($conn) {
            return $conn->real_escape_string($value);
        }, $values);

        $insert = "INSERT INTO `$table` (`" . implode('`, `', $columns) . "`) VALUES ('" . implode("', '", $values) . "');\n";
        fwrite($handle, $insert);
    }

    fwrite($handle, "\n\n");
}

fclose($handle);

// Save the current timestamp to the database
$datetime = new DateTime('now', new DateTimeZone('Asia/Karachi')); // GMT+5 timezone
$backup_datetime = $datetime->format('Y-m-d H:i:s');
$stmt = $conn->prepare("INSERT INTO backup_log (backup_datetime) VALUES (?)");
$stmt->bind_param("s", $backup_datetime);
$stmt->execute();
$stmt->close();

// Send file to the browser
header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename=' . basename($backup_file));
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . filesize($backup_file));
readfile($backup_file);
unlink($backup_file);
exit;
?>
