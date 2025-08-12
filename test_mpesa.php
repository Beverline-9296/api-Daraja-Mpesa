<?php
// Test file for M-Pesa STK Push debugging
include "functions.php";

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>M-Pesa STK Push Test</h2>";

// Test phone number (use your actual phone number for testing)
$test_phone = "0700000000"; // Replace with your phone number
$test_amount = "1";
$test_order = date('YmdHis');

echo "<p><strong>Testing with:</strong></p>";
echo "<ul>";
echo "<li>Phone: $test_phone</li>";
echo "<li>Amount: KSh $test_amount</li>";
echo "<li>Order ID: $test_order</li>";
echo "</ul>";

echo "<p><strong>Initiating STK Push...</strong></p>";

$response = mpesa($test_phone, $test_amount, $test_order);

echo "<p><strong>Response:</strong> $response</p>";

if ($response == '0' || $response == 0) {
    echo "<p style='color: green;'>✅ STK Push initiated successfully! Check your phone for the payment prompt.</p>";
} else {
    echo "<p style='color: red;'>❌ STK Push failed. Response code: $response</p>";
    echo "<p><strong>Check the error logs for more details.</strong></p>";
}

// Display recent error logs
echo "<h3>Recent Error Logs:</h3>";
$error_log = ini_get('error_log');
if (file_exists($error_log)) {
    $logs = file_get_contents($error_log);
    $recent_logs = array_slice(explode("\n", $logs), -10);
    echo "<pre style='background: #f4f4f4; padding: 10px; font-size: 12px;'>";
    foreach ($recent_logs as $log) {
        if (strpos($log, 'M-Pesa') !== false || strpos($log, 'cURL') !== false) {
            echo htmlspecialchars($log) . "\n";
        }
    }
    echo "</pre>";
} else {
    echo "<p>Error log file not found. Check your PHP configuration.</p>";
}

echo "<hr>";
echo "<h3>Important Notes:</h3>";
echo "<ul>";
echo "<li><strong>Callback URL:</strong> Currently set to a placeholder. You need to use ngrok or a public domain for testing.</li>";
echo "<li><strong>Phone Number:</strong> Must be a valid Safaricom number for sandbox testing.</li>";
echo "<li><strong>Credentials:</strong> Using sandbox credentials - these work only in test environment.</li>";
echo "</ul>";

echo "<h3>Next Steps:</h3>";
echo "<ol>";
echo "<li>Replace the placeholder callback URL with a public URL (use ngrok for local testing)</li>";
echo "<li>Test with a valid Safaricom phone number</li>";
echo "<li>Check the M-Pesa response logs above for any errors</li>";
echo "</ol>";
?>
