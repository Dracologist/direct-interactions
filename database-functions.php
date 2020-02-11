<?php
function setup() {
    makeEmployeeTable();
}

function makeEmployeeTable() {
    $link = new mysqli($_SERVER['RDS_HOSTNAME'], $_SERVER['RDS_USERNAME'], $_SERVER['RDS_PASSWORD'], $_SERVER['RDS_DB_NAME'], $_SERVER['RDS_PORT']);
    if (!$link) {
        echo '<script> console.log("Connection Failed: ' . mysqli_connect_error() . '"); </script>';
        die("Connection failed: " . mysqli_connect_error());
    }
    $create_employee_table = "CREATE TABLE IF NOT EXISTS employees ( 
    employeeID INT(255) AUTO_INCREMENT NOT NULL PRIMARY KEY, 
    firstname VARCHAR(50), 
    lastname VARCHAR(50), 
    email VARCHAR(50), 
    password VARCHAR(50), 
    verified BOOLEAN,
    admin BOOLEAN );";
    if($link->query($create_employee_table)){
        echo '<script> console.log("employees table created"); </script>';
    }
    else {
        echo '<script> console.log("failed to create employees table); </script>';
    }
    $link->close();
}
function signup($fname, $lname, $email, $password, $admin) {
    $success = false;
    $link = new mysqli($_SERVER['RDS_HOSTNAME'], $_SERVER['RDS_USERNAME'], $_SERVER['RDS_PASSWORD'], $_SERVER['RDS_DB_NAME'], $_SERVER['RDS_PORT']);
    if (!$link) {
        echo '<script> console.log("Connection Failed: ' . mysqli_connect_error() . '"); </script>';
        die("Connection failed: " . mysqli_connect_error());
    }
    $stmt = $link->prepare("INSERT INTO employees (firstname, lastname, email, password, admin) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssi", $fname, $lname, $email, $password, $admin);
    $success = $stmt->execute();
    if($success){
        echo '<script> console.log("successfully added employee \nFirst Name: ' . $fname 
        . '\nLast Name: ' . $lname . '\nEmail: ' . $email . '\nPassword: ' . $password . '"); </script>';
    }
    else {
        echo '<script> console.log("failed to add employee"); </script>';
    }
    $stmt->close();
    $link->close();
    return $success;
}
function emailTaken($email){
    $link = new mysqli($_SERVER['RDS_HOSTNAME'], $_SERVER['RDS_USERNAME'], $_SERVER['RDS_PASSWORD'], $_SERVER['RDS_DB_NAME'], $_SERVER['RDS_PORT']);
    if (!$link) {
        echo '<script> console.log("Connection Failed: ' . mysqli_connect_error() . '"); </script>';
        die("Connection failed: " . mysqli_connect_error());
    }
    $stmt = $link->prepare("SELECT * FROM employees WHERE email = ?");
    $stmt->bind_param("s", $email);
    $result = $stmt->get_result();
    $stmt->close();
    $link->close();
    return $result->num_rows > 0;
}
function login($email, $password){
    $link = new mysqli($_SERVER['RDS_HOSTNAME'], $_SERVER['RDS_USERNAME'], $_SERVER['RDS_PASSWORD'], $_SERVER['RDS_DB_NAME'], $_SERVER['RDS_PORT']);
    if (!$link) {
        echo '<script> console.log("Connection Failed: ' . mysqli_connect_error() . '"); </script>';
        die("Connection failed: " . mysqli_connect_error());
    }
    $stmt = $link->prepare("SELECT * FROM employees WHERE email = ? AND password = ?");
    $stmt->bind_param("ss", $email, $password);
    $result = $stmt->get_result();
    $stmt->close();
    $link->close();
    return $result->num_rows > 0;
}
function admin($email, $password) {
    $link = new mysqli($_SERVER['RDS_HOSTNAME'], $_SERVER['RDS_USERNAME'], $_SERVER['RDS_PASSWORD'], $_SERVER['RDS_DB_NAME'], $_SERVER['RDS_PORT']);
    if (!$link) {
        echo '<script> console.log("Connection Failed: ' . mysqli_connect_error() . '"); </script>';
        die("Connection failed: " . mysqli_connect_error());
    }
    $stmt = $link->prepare("SELECT * FROM employees WHERE email = ? AND password = ? AND admin");
    $stmt->bind_param("ss", $email, $password);
    $result = $stmt->get_result();
    $stmt->close();
    $link->close();
    return $result->num_rows > 0;
}