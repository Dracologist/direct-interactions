<?php
function setup() {
    makeEmployeeTable();
}
function connect(){
    $link = new mysqli($_SERVER['RDS_HOSTNAME'], $_SERVER['RDS_USERNAME'], $_SERVER['RDS_PASSWORD'], $_SERVER['RDS_DB_NAME'], $_SERVER['RDS_PORT']);
    return $link;
}
function makeEmployeeTable() {
    $link = connect();
    $create_employee_table = "CREATE TABLE IF NOT EXISTS employees ( 
    employeeID INT(255) AUTO_INCREMENT NOT NULL PRIMARY KEY, 
    firstname VARCHAR(50), 
    lastname VARCHAR(50), 
    email VARCHAR(50), 
    password VARCHAR(50), 
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
    $link = connect();
    $stmt = $link->prepare("INSERT INTO employees (firstname, lastname, email, password, admin) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssi", $fname, $lname, $email, $password, $admin);
    if($stmt->execute()){
        echo '<script> console.log("employees added successfully"); </script>';
    }
    else {
        echo '<script> console.log("failed to add employee"); </script>';
    }
    $stmt->close();
    $link->close();
}
function emailTaken($email){
    $link = connect();
    $stmt = $link->prepare("SELECT * FROM employees WHERE email = ?");
    $stmt->bind_param("s", $email);
    $result = $stmt->get_result();
    $stmt->close();
    $link->close();
    return $result->num_rows > 0;
}
function login($email, $password){
    $link = connect();
    $stmt = $link->prepare("SELECT * FROM employees WHERE email = ? AND password = ?");
    $stmt->bind_param("ss", $email, $password);
    $result = $stmt->get_result();
    $stmt->close();
    $link->close();
    return $result->num_rows > 0;
}
function admin($email, $password) {
    $link = connect();
    $stmt = $link->prepare("SELECT * FROM employees WHERE email = ? AND password = ? AND admin");
    $stmt->bind_param("ss", $email, $password);
    $result = $stmt->get_result();
    $stmt->close();
    $link->close();
    return $result->num_rows > 0;
}