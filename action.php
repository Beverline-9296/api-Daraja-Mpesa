<?php
include "functions.php";

if (isset($_POST['submit'])) {
    $phone = $_POST['phone'];
    $amount = $_POST['amount'];
    $package = $_POST['package'];
    $invoice = date('YmdHis');
    
    // Validate phone number
    if (empty($phone) || !preg_match('/^(0|254)[0-9]{9}$/', $phone)) {
        header("location:index.php?error=Please enter a valid phone number");
        exit();
    }
    
    // Validate amount
    if (!in_array($amount, ['100', '500', '1000'])) {
        header("location:index.php?error=Invalid donation amount");
        exit();
    }
    
    try {
        // Store donation record in database
        $conn = db_conn();
        $stmt = $conn->prepare("INSERT INTO donations (order_id, package_name, amount, phone_number, status) VALUES (?, ?, ?, ?, 'Pending')");
        $stmt->execute([$invoice, $package, $amount, $phone]);
        
        // Initiate M-Pesa payment
        $response = mpesa($phone, $amount, $invoice);

        if ($response == '0' || $response == 0) {
            header("location:index.php?success=Payment request sent! Please check your phone and enter your M-Pesa PIN to complete the donation.");
        } elseif ($response == 'CURL_ERROR') {
            $stmt = $conn->prepare("UPDATE donations SET status = 'Failed' WHERE order_id = ?");
            $stmt->execute([$invoice]);
            header("location:index.php?error=Network error occurred. Please check your internet connection and try again.");
        } elseif ($response == 'TOKEN_ERROR') {
            $stmt = $conn->prepare("UPDATE donations SET status = 'Failed' WHERE order_id = ?");
            $stmt->execute([$invoice]);
            header("location:index.php?error=M-Pesa service temporarily unavailable. Please try again later.");
        } else {
            // Update status to failed
            $stmt = $conn->prepare("UPDATE donations SET status = 'Failed' WHERE order_id = ?");
            $stmt->execute([$invoice]);
            header("location:index.php?error=Payment request failed (Code: $response). Please try again.");
        }
        
        $conn = null;
        
    } catch (Exception $e) {
        error_log("Error processing donation: " . $e->getMessage());
        header("location:index.php?error=System error occurred. Please try again later.");
    }
}
