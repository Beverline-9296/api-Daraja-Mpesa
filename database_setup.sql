-- Database setup for Daraja API M-Pesa Donation Platform
-- Run this SQL script in phpMyAdmin or MySQL command line

CREATE DATABASE IF NOT EXISTS stkpush;
USE stkpush;

-- Table to store donation transactions
CREATE TABLE IF NOT EXISTS donations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id VARCHAR(50) UNIQUE NOT NULL,
    package_name VARCHAR(20) NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    phone_number VARCHAR(15) NOT NULL,
    status ENUM('Pending', 'Completed', 'Failed') DEFAULT 'Pending',
    mpesa_receipt_number VARCHAR(50) NULL,
    transaction_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    callback_received BOOLEAN DEFAULT FALSE,
    result_code INT NULL,
    result_desc TEXT NULL
);

-- Table to store M-Pesa callback logs
CREATE TABLE IF NOT EXISTS callback_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id VARCHAR(50) NOT NULL,
    callback_data TEXT NOT NULL,
    received_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    processed BOOLEAN DEFAULT FALSE
);

-- Insert sample data (optional)
INSERT INTO donations (order_id, package_name, amount, phone_number, status) VALUES
('20240101120000', 'Bronze', 100.00, '254700000000', 'Pending'),
('20240101120001', 'Silver', 500.00, '254700000001', 'Completed'),
('20240101120002', 'Gold', 1000.00, '254700000002', 'Failed');
