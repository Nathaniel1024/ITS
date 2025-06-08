CREATE DATABASE its_db;
use its_db;

CREATE TABLE users (
   id INT NOT NULL AUTO_INCREMENT ,
   name VARCHAR(255) NOT NULL ,
   email VARCHAR(255) NOT NULL ,
   password VARCHAR(255) NOT NULL ,
   PRIMARY KEY (id),
   UNIQUE (email)
);

CREATE TABLE password_resets (
   id int(11) NOT NULL,
   email varchar(255) NOT NULL,
   token varchar(100) NOT NULL,
   expires datetime(6) NOT NULL
); 

CREATE TABLE transaction_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    transaction_id INT NOT NULL,
    payer_name VARCHAR(255) NOT NULL,
    transaction_type ENUM('Collection', 'Disbursement') NOT NULL,
    amount DOUBLE NOT NULL,
    transaction_date DATE NOT NULL,
    transaction_time TIME NOT NULL,
    guarantor VARCHAR(100) NOT NULL,
    update_status ENUM('Completed','Awaiting Approval','Verified') NOT NULL,
    action ENUM('Insert', 'Update', 'Delete') NOT NULL,
    timestamp DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    payer_name VARCHAR(255) NOT NULL,
    transaction_type ENUM('Collection', 'Disbursement') NOT NULL,
    amount DOUBLE NOT NULL,
    transaction_date DATE NOT NULL,
    transaction_time TIME NOT NULL,
    guarantor VARCHAR(100) NOT NULL,
    update_status ENUM('Completed','Awaiting Approval','Verified') NOT NULL
);

CREATE TABLE tracking_details (
    id INT AUTO_INCREMENT PRIMARY KEY,
    transaction_id INT NOT NULL,
    tracking_id VARCHAR(50) NOT NULL UNIQUE,
    department VARCHAR(50) NOT NULL,
    payment_method ENUM('Cash', 'Check', 'Bank transfer') NOT NULL,
    status ENUM('Pending', 'Cleared', 'Reconciled', 'Flagged', 'Void') NOT NULL,
    treasurer_remarks TEXT,
    date_made DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (transaction_id) REFERENCES transactions(id)
);

CREATE TABLE tracking_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tracking_id VARCHAR(50) NOT NULL,
    changed_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    changed_by VARCHAR(100),
    change_description TEXT,
    FOREIGN KEY (tracking_id) REFERENCES tracking_details(tracking_id)
);

CREATE TABLE `budget` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `budget` double NOT NULL,
  `sector` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
);

INSERT INTO `budget` (`id`, `budget`, `sector`) VALUES
(5, 33000000, 'Education'),
(6, 22000000, 'Infrastructure'),
(7, 16500000, 'Public Safety'),
(8, 11000000, 'Health and Sanitation'),
(9, 13200000, 'Administrative Costs'),
(10, 8800000, 'Environmental Services'),
(11, 5500000, 'Others / Miscellaneous');