CREATE TABLE collections (
    id INT AUTO_INCREMENT PRIMARY KEY,
    receipt_no VARCHAR(50) NOT NULL,
    payer_name VARCHAR(255) NOT NULL,
    revenue_type VARCHAR(100) NOT NULL,
    amount DOUBLE NOT NULL,
    collection_date DATE NOT NULL,
    time_collected TIME NOT NULL,
    collector VARCHAR(100) NOT NULL,
    remarks TEXT
);
