<?php
// Database connection
include 'connect.php';

// Initialize total balances
$total_supplier_balance = 0;
$total_customer_balance = 0;
$total_today_purchase = 0;
$total_today_sale = 0;

// Calculate total supplier closing balance
$sql_suppliers = "SELECT code FROM supplier WHERE stat = 0";
$result_suppliers = $conn->query($sql_suppliers);

if ($result_suppliers && $result_suppliers->num_rows > 0) {
    while ($row = $result_suppliers->fetch_assoc()) {
        $code = $row["code"];
        $closing_balance = 0;

        $balance_sql = "
            SELECT datetime, id, NULL AS debit, priceAfterDiscount AS credit, 'Purchase' AS type 
            FROM purchases 
            WHERE supplier_code = ? 
            UNION ALL
            SELECT datetime, id, priceAfterDiscount AS debit, NULL AS credit, 'Purchase Return' AS type 
            FROM purchases_return 
            WHERE supplier_code = ? 
            UNION ALL
            SELECT datetime, id, receivedAmount AS debit, NULL AS credit, 'Purchase Payment' AS type 
            FROM purchases 
            WHERE supplier_code = ? 
            UNION ALL
            SELECT datetime, id, NULL AS debit, receivedAmount AS credit, 'Purchase Return Payment' AS type 
            FROM purchases_return 
            WHERE supplier_code = ? 
            UNION ALL
            SELECT dateTime AS datetime, id, payment AS debit, NULL AS credit, 'Payment Sent' AS type 
            FROM supplier_payment_sent 
            WHERE supplier_id = ? 
            UNION ALL
            SELECT dateTime AS datetime, id, NULL AS debit, payment AS credit, 'Payment Received' AS type 
            FROM supplier_payment_received 
            WHERE supplier_id = ? 
            ORDER BY datetime ASC";

        if ($balance_stmt = $conn->prepare($balance_sql)) {
            $balance_stmt->bind_param("iiiiii", $code, $code, $code, $code, $code, $code);
            $balance_stmt->execute();
            $balance_result = $balance_stmt->get_result();

            while ($balance_row = $balance_result->fetch_assoc()) {
                if ($balance_row['debit'] > 0) {
                    $closing_balance -= $balance_row['debit'];
                }
                if ($balance_row['credit'] > 0) {
                    $closing_balance += $balance_row['credit'];
                }
            }
            $balance_stmt->close();
        }

        $total_supplier_balance += $closing_balance;
    }
}

// Calculate total customer closing balance
$sql_customers = "SELECT code FROM customer WHERE stat = 0";
$result_customers = $conn->query($sql_customers);

if ($result_customers && $result_customers->num_rows > 0) {
    while ($row = $result_customers->fetch_assoc()) {
        $code = $row["code"];
        $final_balance = 0;

        $balance_sql = "
            SELECT datetime, id, priceAfterDiscount AS debit, receivedAmount AS credit, 'Sale' AS type 
            FROM sales 
            WHERE customer_code = ? 
            UNION ALL
            SELECT datetime, id, receivedAmount AS debit, priceAfterDiscount AS credit, 'Sale Return' AS type 
            FROM sales_return 
            WHERE customer_code = ? 
            UNION ALL
            SELECT dateTime AS datetime, id, payment AS debit, NULL AS credit, 'Payment Sent' AS type 
            FROM customer_payment_sent 
            WHERE customer_id = ? 
            UNION ALL
            SELECT dateTime AS datetime, id, NULL AS debit, payment AS credit, 'Payment Received' AS type 
            FROM customer_payment_received 
            WHERE customer_id = ? 
            ORDER BY datetime ASC";

        if ($balance_stmt = $conn->prepare($balance_sql)) {
            $balance_stmt->bind_param("iiii", $code, $code, $code, $code);
            $balance_stmt->execute();
            $balance_result = $balance_stmt->get_result();

            while ($balance_row = $balance_result->fetch_assoc()) {
                if ($balance_row['debit'] > 0) {
                    $final_balance -= $balance_row['debit'];
                }
                if ($balance_row['credit'] > 0) {
                    $final_balance += $balance_row['credit'];
                }
            }
            $balance_stmt->close();
        }

        $total_customer_balance += $final_balance;
    }
}

// Calculate today's total purchase
$today = date('Y-m-d');
$sql_today_purchase = "SELECT SUM(priceAfterDiscount) AS total_purchase FROM purchases WHERE DATE(datetime) = ?";
$stmt_today_purchase = $conn->prepare($sql_today_purchase);
$stmt_today_purchase->bind_param("s", $today);
$stmt_today_purchase->execute();
$result_today_purchase = $stmt_today_purchase->get_result();
if ($result_today_purchase && $row = $result_today_purchase->fetch_assoc()) {
    $total_today_purchase = $row['total_purchase'];
}
$stmt_today_purchase->close();

// Calculate today's total sale
$sql_today_sale = "SELECT SUM(priceAfterDiscount) AS total_sale FROM sales WHERE DATE(datetime) = ?";
$stmt_today_sale = $conn->prepare($sql_today_sale);
$stmt_today_sale->bind_param("s", $today);
$stmt_today_sale->execute();
$result_today_sale = $stmt_today_sale->get_result();
if ($result_today_sale && $row = $result_today_sale->fetch_assoc()) {
    $total_today_sale = $row['total_sale'];
}
$stmt_today_sale->close();

// Function to format balance with brackets if negative
function format_balance($balance) {
    return $balance >= 0 ? number_format($balance, 2) : '(' . number_format(abs($balance), 2) . ')';
}

// Close the database connection
$conn->close();
?>
