<?php
function setup() {
    makeEmployeeTable();
}
function connect(){
    $dbhost = $_SERVER['RDS_HOSTNAME'];
    $dbport = $_SERVER['RDS_PORT'];
    $dbname = $_SERVER['RDS_DB_NAME'];
    $charset = 'utf8' ;
    
    $dsn = "mysql:host={$dbhost};port={$dbport};dbname={$dbname};charset={$charset}";
    $username = $_SERVER['RDS_USERNAME'];
    $password = $_SERVER['RDS_PASSWORD'];
    
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    return $pdo;
}
function makeEmployeeTable() {
    $pdo = connect();
    $create_employee_table = "CREATE TABLE IF NOT EXISTS employees (".
        "employeeID int AUTO_INCREMENT NOT NULL PRIMARY KEY,".
        "firstname VARCHAR(50),".
        "lastname VARCHAR(50),".
        "email VARCHAR(50),".
        "password VARCHAR(50),".
        "admin BOOLEAN);";
    $pdo->exec($create_employee_table);
}
function signup($fname, $lname, $email, $password, $admin) {
    $pdo = connect();
    $stmt = $pdo->prepare("INSERT INTO employees (firstname, lastname, email, password, admin) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssi", $fn, $ln, $em, $pw, $ad);
    $fn = $fname;
    $ln = $lname;
    $em = $email;
    $pw = $password;
    $ad = $admin;
    $stmt->execute();
    $stmt->close();
}
function emailTaken($email){
    $pdo = connect();
    $stmt = $pdo->prepare("SELECT * FROM employees WHERE email = ?");
    $stmt->bind_param("s", $em);
    $em = $email;
    $stmt->execute();
    return $stmt->rowCount() > 0;
}
function login($email, $password){
    $pdo = connect();
    $stmt = $pdo->prepare("SELECT * FROM employees WHERE email = ? AND password = ?");
    $stmt->bind_param("ss", $em, $pw);
    $em = $email;
    $pw = $password;
    $stmt->execute();
    return $stmt->rowCount() > 0;
}
function admin($email, $password) {
    $pdo = connect();
    $stmt = $pdo->prepare("SELECT * FROM employees WHERE email = ? AND password = ? AND admin");
    $stmt->bind_param("ss", $em, $pw);
    $em = $email;
    $pw = $password;
    $stmt->execute();
    return $stmt->rowCount() > 0;
}