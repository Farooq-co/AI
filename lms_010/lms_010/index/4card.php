<?php
require 'db.php';

// Fetch the data from the database
$stmt = $pdo->query("SELECT * FROM statistics WHERE id = 1");
$data = $stmt->fetch(PDO::FETCH_ASSOC);

// Assign values to variables
$total_today_purchase = $data['total_today_purchase'];
$total_supplier_balance = $data['total_supplier_balance'];
$total_today_sale = $data['total_today_sale'];
$total_customer_balance = $data['total_customer_balance'];
?>

<div class="col-md-6 grid-margin transparent">
    <div class="row">

        <div class="col-md-6 mb-4 stretch-card transparent">
            <div class="card card-tale">
                <div class="card-body">
                    <p class="mb-4">Number of Downloads</p>
                    <p id="purchase" class="fs-30 mb-2"><?php echo $total_today_purchase; ?></p>
                    <p><a href="" class="text-white">View Details</a></p>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-4 stretch-card transparent">
            <div class="card card-dark-blue">
                <div class="card-body">
                    <p class="mb-4">Average Download Speed</p>
                    <p id="supplier" class="fs-30 mb-2"><?php echo $total_supplier_balance . ' MB'; ?></p>
                    <p><a href="" class="text-white">View Details</a></p>
                </div>
            </div>
        </div>

    </div>
    <div class="row">

        <div class="col-md-6 mb-4 mb-lg-0 stretch-card transparent">
            <div class="card card-light-blue">
                <div class="card-body">
                    <p class="mb-4">Total Data Downloaded</p>
                    <p id="sale" class="fs-30 mb-2"><?php echo number_format($total_today_sale / 1000, 2) . ' GB'; ?></p>
                    <p><a href="" class="text-white">View Details</a></p>
                </div>
            </div>
        </div>

        <div class="col-md-6 stretch-card transparent">
            <div class="card card-light-danger">
                <div class="card-body">
                    <p class="mb-4">Downloads in Last Minute</p>
                    <p id="customer" class="fs-30 mb-2"><?php echo $total_customer_balance; ?></p>
                    <p><a href="" class="text-white">View Details</a></p>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
// Function to update values on the server
function updateServerValue(key, value) {
    fetch('index/update_session.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ key, value })
    });
}

// Function to generate a random integer between a range
function getRandomInt(min, max) {
    return Math.floor(Math.random() * (max - min + 1)) + min;
}

// Variables to keep track of data states
let numberOfDownloads = <?php echo $total_today_purchase; ?>;
let totalDataDownloaded = <?php echo $total_today_sale; ?>;

// Update the card values periodically (every 3 seconds)
setInterval(() => {
    // Increment the values
    numberOfDownloads += getRandomInt(1, 5); // Increment by 1 to 5
    totalDataDownloaded += getRandomInt(50, 100); // Increment by 50 to 100MB

    // Update DOM
    document.getElementById('purchase').innerText = numberOfDownloads;
    const displayData = totalDataDownloaded >= 1000000
        ? (totalDataDownloaded / 1000000).toFixed(2) + ' TB'
        : (totalDataDownloaded / 1000).toFixed(2) + ' GB';
    document.getElementById('sale').innerText = displayData;

    // Update server values
    updateServerValue('total_today_purchase', numberOfDownloads);
    updateServerValue('total_today_sale', totalDataDownloaded);

    // Update Average Download Speed
    const downloadSpeed = getRandomInt(20, 100); // Random between 20MB and 100MB
    document.getElementById('supplier').innerText = downloadSpeed + ' MB';
}, 3000); // Update every 3 seconds

// Update "Downloads in Last Minute" every 30 seconds
setInterval(() => {
    const lastDownloadTime = getRandomInt(2, 90); // Random number between 2 and 90
    document.getElementById('customer').innerText = lastDownloadTime;

    // Optionally update the server
    updateServerValue('total_customer_balance', lastDownloadTime);
}, 30000); // Update every 30 seconds
</script>
