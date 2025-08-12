<?php
include "functions.php";

$conn = db_conn();
$invoice = $_GET['orderid'];

$callbackJSONData=file_get_contents('php://input');

$logFile = "stkPush.json";
$log = fopen($logFile, "a");
fwrite($log, $callbackJSONData . " - Order ID: " . $invoice . "\n");
fclose($log);

$callbackData=json_decode($callbackJSONData);

$resultCode=$callbackData->Body->stkCallback->ResultCode;
$resultDesc= $callbackData->Body->stkCallback->ResultDesc;

// Extract amount and phone from callback data
$amount = 0;
$phone = '';
if(isset($callbackData->Body->stkCallback->CallbackMetadata->Item)) {
    foreach($callbackData->Body->stkCallback->CallbackMetadata->Item as $item) {
        if($item->Name == 'Amount') {
            $amount = $item->Value;
        }
        if($item->Name == 'PhoneNumber') {
            $phone = $item->Value;
        }
    }
}

$orderid = strval($invoice);
$amount = strval($amount);

if($resultCode == 0){
    // Payment successful - update database
    try {
        $stmt = $conn->prepare("UPDATE donations SET status = 'Completed', callback_received = TRUE, result_code = ?, result_desc = ? WHERE order_id = ?");
        $stmt->execute([$resultCode, $resultDesc, $orderid]);
        
        // Log callback data
        $stmt = $conn->prepare("INSERT INTO callback_logs (order_id, callback_data, processed) VALUES (?, ?, TRUE)");
        $stmt->execute([$orderid, $callbackJSONData]);
        
    } catch(PDOException $e) {
        error_log("Database error in callback: " . $e->getMessage());
    }
} else {
    // Payment failed - update database
    try {
        $stmt = $conn->prepare("UPDATE donations SET status = 'Failed', callback_received = TRUE, result_code = ?, result_desc = ? WHERE order_id = ?");
        $stmt->execute([$resultCode, $resultDesc, $orderid]);
        
        // Log callback data
        $stmt = $conn->prepare("INSERT INTO callback_logs (order_id, callback_data, processed) VALUES (?, ?, TRUE)");
        $stmt->execute([$orderid, $callbackJSONData]);
        
    } catch(PDOException $e) {
        error_log("Database error in callback: " . $e->getMessage());
    }
}

$conn = null;
?>