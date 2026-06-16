<?php

// Common helper functions for fee management modules.

function sanitizeText($value)
{
    return trim(filter_var($value, FILTER_UNSAFE_RAW, FILTER_FLAG_NO_ENCODE_QUOTES));
}

function sanitizeInt($value)
{
    return intval(filter_var($value, FILTER_VALIDATE_INT, ['options' => ['default' => 0]]));
}

function sanitizeFloat($value)
{
    return floatval(filter_var($value, FILTER_VALIDATE_FLOAT, ['options' => ['default' => 0.00]]));
}

function jsonResponse($success, $message, $data = [])
{
    header('Content-Type: application/json; charset=utf-8');
    // Always return a predictable structure with optional data key
    $payload = ['success' => (bool)$success, 'message' => $message, 'data' => $data];
    echo json_encode($payload);
    exit;
}

function getPaymentMethods($conn)
{
    $methods = [];
    if (!isset($conn) || !($conn instanceof mysqli)) {
        return $methods;
    }
    $sql = "SELECT id, method_name FROM payment_methods WHERE status = 'Active' ORDER BY method_name";
    if ($result = $conn->query($sql)) {
        while ($row = $result->fetch_assoc()) {
            $methods[] = $row;
        }
        $result->free();
    }
    return $methods;
}

function generateInvoiceNo($conn)
{
    if (!isset($conn) || !($conn instanceof mysqli)) {
        return 'INV' . date('Ymd') . '0001';
    }
    $invoiceNo = 'INV' . date('Ymd') . '0001';
    $sql = "SELECT invoice_no FROM fee_invoices ORDER BY id DESC LIMIT 1";
    if ($res = $conn->query($sql)) {
        $row = $res->fetch_assoc();
        if (!empty($row['invoice_no'])) {
            $lastDigits = preg_replace('/[^0-9]/', '', $row['invoice_no']);
            $nextNumber = intval($lastDigits) + 1;
            $invoiceNo = 'INV' . str_pad($nextNumber, 10, '0', STR_PAD_LEFT);
        }
        $res->free();
    }
    return $invoiceNo;
}

function generateReceiptNo($conn)
{
    if (!isset($conn) || !($conn instanceof mysqli)) {
        return 'REC' . date('Ymd') . '0001';
    }
    $receiptNo = 'REC' . date('Ymd') . '0001';
    $sql = "SELECT receipt_no FROM fee_payments ORDER BY id DESC LIMIT 1";
    if ($res = $conn->query($sql)) {
        $row = $res->fetch_assoc();
        if (!empty($row['receipt_no'])) {
            $lastDigits = preg_replace('/[^0-9]/', '', $row['receipt_no']);
            $nextNumber = intval($lastDigits) + 1;
            $receiptNo = 'REC' . str_pad($nextNumber, 10, '0', STR_PAD_LEFT);
        }
        $res->free();
    }
    return $receiptNo;
}

function getSessionOptions($conn)
{
    $items = [];
    if (!isset($conn) || !($conn instanceof mysqli)) {
        return $items;
    }
    // sessions table may not have start_date; order by id as a safe fallback
    $sql = "SELECT id, name FROM sessions WHERE COALESCE(status, 'Active') = 'Active' ORDER BY id DESC";
    if ($res = $conn->query($sql)) {
        while ($row = $res->fetch_assoc()) {
            $items[] = $row;
        }
        $res->free();
    }
    return $items;
}

function getClassOptions($conn)
{
    $items = [];
    if (!isset($conn) || !($conn instanceof mysqli)) {
        return $items;
    }
    $sql = "SELECT id, name FROM classes WHERE COALESCE(status, 'Active') = 'Active' ORDER BY name";
    if ($res = $conn->query($sql)) {
        while ($row = $res->fetch_assoc()) {
            $items[] = $row;
        }
        $res->free();
    }
    return $items;
}

function getStudentOptions($conn)
{
    $items = [];
    if (!isset($conn) || !($conn instanceof mysqli)) {
        return $items;
    }
    $sql = "SELECT id, student_name FROM students WHERE COALESCE(status, 'Active') = 'Active' ORDER BY student_name";
    if ($res = $conn->query($sql)) {
        while ($row = $res->fetch_assoc()) {
            $items[] = $row;
        }
        $res->free();
    }
    return $items;
}

function getPackageOptions($conn)
{
    $items = [];
    if (!isset($conn) || !($conn instanceof mysqli)) {
        return $items;
    }
    $sql = "SELECT id, name, total_amount FROM fee_packages WHERE COALESCE(status, 'Active') = 'Active' ORDER BY name";
    if ($res = $conn->query($sql)) {
        while ($row = $res->fetch_assoc()) {
            $items[] = $row;
        }
        $res->free();
    }
    return $items;
}

function getInvoiceStatusOptions()
{
    return ['Pending', 'Paid', 'Partially Paid', 'Overdue', 'Cancelled'];
}

function getPaymentStatusOptions()
{
    return ['Pending', 'Verified', 'Completed', 'Failed', 'Refunded'];
}

function getDiscountTypes()
{
    return ['Sibling Discount', 'Employee Child', 'Merit Scholarship', 'Need Based', 'Special Concession'];
}

function getFineTypes()
{
    return ['Fixed Fine', 'Per Day Fine', 'Percentage Fine'];
}

function getWaiverTypes()
{
    return ['Admission Fee', 'Exam Fee', 'Transport Fee', 'Library Fee', 'Fine', 'Tuition Fee'];
}

function calculateRemainingBalance($invoice)
{
    $paid = floatval($invoice['total_paid'] ?? 0);
    $total = floatval($invoice['total_amount'] ?? 0);
    return max(0.00, $total - $paid);
}

function buildSelectOptions($items, $selected = null)
{
    $html = '';
    foreach ($items as $item) {
        $value = $item['id'] ?? ($item['value'] ?? '');
        $display = $item['name'] ?? $item['student_name'] ?? $item['method_name'] ?? ($item['title'] ?? $value);
        $isSelected = ($selected !== null && (string)$selected === (string)$value) ? ' selected' : '';
        $html .= '<option value="' . htmlspecialchars($value) . '"' . $isSelected . '>' . htmlspecialchars($display) . '</option>';
    }
    return $html;
}

function calculateFineAmount($amount, $fineType, $rate, $daysOverdue = 0)
{
    if ($amount <= 0) {
        return 0.00;
    }
    switch ($fineType) {
        case 'Fixed Fine':
            return floatval($rate);
        case 'Per Day Fine':
            return floatval($rate) * intval($daysOverdue);
        case 'Percentage Fine':
            return ($amount * floatval($rate)) / 100.0;
        default:
            return 0.00;
    }
}

function formatMoney($value)
{
    return number_format(floatval($value), 2);
}

?>