<?php

$conn = new mysqli('localhost', 'root', 'Mysqlisbest@1', 'banking_system_db');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}