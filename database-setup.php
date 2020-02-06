<?php
$dbhost = $_SERVER['RDS_HOSTNAME'];
$dbport = $_SERVER['RDS_PORT'];
$dbname = $_SERVER['RDS_DB_NAME'];
$charset = 'utf8' ;

$dsn = "mysql:host={$dbhost};port={$dbport};dbname={$dbname};charset={$charset}";
$username = $_SERVER['RDS_USERNAME'];
$password = $_SERVER['RDS_PASSWORD'];

$pdo = new PDO($dsn, $username, $password);

$create_employee_table = "CREATE TABLE IF NOT EXISTS employees (
employeeID int AUTO_INCREMENT NOT NULL PRIMARY KEY,
firstname VARCHAR(50),
lastname VARCHAR(50),
email VARCHAR(50),
password VARCHAR(50),
admin BOOLEAN);";
$pdo->exec($create_employee_table);