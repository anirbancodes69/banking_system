1. Create Database and Tables

create schema banking_system_db;

use banking_system_db;

CREATE TABLE users (
id INT AUTO_INCREMENT PRIMARY KEY,
username VARCHAR(50) NOT NULL UNIQUE,
password VARCHAR(255) NOT NULL,
status ENUM('pending', 'approved') DEFAULT 'pending',
is_admin BOOLEAN DEFAULT FALSE
);

CREATE TABLE transactions (
id INT AUTO_INCREMENT PRIMARY KEY,
user_id INT,
type ENUM('credit', 'withdraw', 'cheque') NOT NULL,
amount DECIMAL(10, 2) NOT NULL,
approved BOOLEAN DEFAULT FALSE,
date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
FOREIGN KEY (user_id) REFERENCES users(id)

);

CREATE TABLE queue (
id INT AUTO_INCREMENT PRIMARY KEY,
transaction_id INT NOT NULL,
status ENUM('pending', 'processing', 'completed') DEFAULT 'pending',
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
FOREIGN KEY (transaction_id) REFERENCES transactions(id)
);

2. Modify dbcon.php for database setup and connection.

3. For admin users: username can be anything, password is: 123456789

4. Normal user account creation needs approval from admin.

5. After approval user can credit, debit and send cheques.

6. Sending checks needs approval from admin.

7. The cheque approval part is implemented using database queue and database transaction property.

8. Remember to run cron/admin_cheque_approval in cron/scheduler every hour/day for dequeing the queue at database.

9. After approval the status is reflected at the user end.
