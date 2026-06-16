<?php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);

    if (isset($input['key']) && isset($input['value'])) {
        $key = $input['key'];
        $value = intval($input['value']); // Ensure the value is an integer

        // Map the key to the corresponding database column
        $validKeys = [
            'total_today_purchase' => 'total_today_purchase',
            'total_today_sale' => 'total_today_sale',
            'total_supplier_balance' => 'total_supplier_balance',
            'total_customer_balance' => 'total_customer_balance'
        ];

        if (array_key_exists($key, $validKeys)) {
            $column = $validKeys[$key];

            // Update the specific value in the database
            $stmt = $pdo->prepare("UPDATE statistics SET $column = :value WHERE id = 1");
            $stmt->execute(['value' => $value]);

            echo json_encode(['status' => 'success']);
            exit;
        }
    }
    echo json_encode(['status' => 'error', 'message' => 'Invalid key or value']);
} else {
    // Fetch the current data from the database
    $stmt = $pdo->query("SELECT * FROM statistics WHERE id = 1");
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode($data);
}
?>
