<?php
include 'index/4card_php.php';
include 'connect.php';

// Fetch total purchase value for the last 30 days
$sql_purchase_total = "SELECT SUM(priceAfterDiscount) AS totalPurchaseValue FROM purchases WHERE datetime >= NOW() - INTERVAL 30 DAY";
$result_purchase_total = $conn->query($sql_purchase_total);
$row_purchase_total = $result_purchase_total->fetch_assoc();
$totalPurchaseValue = $row_purchase_total['totalPurchaseValue'];

// Fetch total sales value for the last 30 days
$sql_sales_total = "SELECT SUM(priceAfterDiscount) AS totalSalesValue FROM sales WHERE datetime >= NOW() - INTERVAL 30 DAY";
$result_sales_total = $conn->query($sql_sales_total);
$row_sales_total = $result_sales_total->fetch_assoc();
$totalSalesValue = $row_sales_total['totalSalesValue'];

// Fetch daily purchase values for the last 30 days
$sql_daily_purchase = "SELECT DATE(datetime) AS date, SUM(priceAfterDiscount) AS dailyPurchaseValue FROM purchases WHERE datetime >= NOW() - INTERVAL 30 DAY GROUP BY DATE(datetime)";
$result_daily_purchase = $conn->query($sql_daily_purchase);
$dailyPurchaseValues = [];
while($row = $result_daily_purchase->fetch_assoc()) {
    $dailyPurchaseValues[] = $row;
}

// Fetch daily sales values for the last 30 days
$sql_daily_sales = "SELECT DATE(datetime) AS date, SUM(priceAfterDiscount) AS dailySalesValue FROM sales WHERE datetime >= NOW() - INTERVAL 30 DAY GROUP BY DATE(datetime)";
$result_daily_sales = $conn->query($sql_daily_sales);
$dailySalesValues = [];
while($row = $result_daily_sales->fetch_assoc()) {
    $dailySalesValues[] = $row;
}

// Fetch monthly purchase values
$sql_monthly_purchase = "SELECT DATE_FORMAT(datetime, '%Y-%m') AS month, SUM(priceAfterDiscount) AS monthlyPurchaseValue FROM purchases GROUP BY month";
$result_monthly_purchase = $conn->query($sql_monthly_purchase);
$monthlyPurchaseValues = [];
while($row = $result_monthly_purchase->fetch_assoc()) {
    $monthlyPurchaseValues[] = $row;
}

// Fetch monthly sales values
$sql_monthly_sales = "SELECT DATE_FORMAT(datetime, '%Y-%m') AS month, SUM(priceAfterDiscount) AS monthlySalesValue FROM sales GROUP BY month";
$result_monthly_sales = $conn->query($sql_monthly_sales);
$monthlySalesValues = [];
while($row = $result_monthly_sales->fetch_assoc()) {
    $monthlySalesValues[] = $row;
}

$conn->close();
?>
