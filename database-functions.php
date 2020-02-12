<?php
function setup() {
    makeEmployeeTable();
    makeShiftTable();
    makeTeamEmployeeTable();
    makeTeamShiftTable();
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
    admin BOOLEAN )";
    if($link->query($create_employee_table)){
        echo '<script> console.log("employees table created"); </script>';
    }
    else {
        echo '<script> console.log("failed to create employees table); </script>';
    }
    $link->close();
}

function makeShiftTable() {
    $link = new mysqli($_SERVER['RDS_HOSTNAME'], $_SERVER['RDS_USERNAME'], $_SERVER['RDS_PASSWORD'], $_SERVER['RDS_DB_NAME'], $_SERVER['RDS_PORT']);
    if (!$link) {
        echo '<script> console.log("Connection Failed: ' . mysqli_connect_error() . '"); </script>';
        die("Connection failed: " . mysqli_connect_error());
    }
    $create_shift_table = "CREATE TABLE IF NOT EXISTS shifts (
    shiftID INT(255) AUTO_INCREMENT NOT NULL PRIMARY KEY,
    time TIMESTAMP,
    employee INT(255),
    taken BOOLEAN )";
    if($link->query($create_shift_table)){
        echo '<script> console.log("shifts table created"); </script>';
    }
    else {
        echo '<script> console.log("failed to create shifts table); </script>';
    }
    $link->close();
}

function makeTeamEmployeeTable() {
    $link = new mysqli($_SERVER['RDS_HOSTNAME'], $_SERVER['RDS_USERNAME'], $_SERVER['RDS_PASSWORD'], $_SERVER['RDS_DB_NAME'], $_SERVER['RDS_PORT']);
    if (!$link) {
        echo '<script> console.log("Connection Failed: ' . mysqli_connect_error() . '"); </script>';
        die("Connection failed: " . mysqli_connect_error());
    }
    $create_shift_table = "CREATE TABLE IF NOT EXISTS teamemployees (
    teamName VARCHAR(150),
    employeeID INT(255) NOT NULL FOREIGN KEY REFERENCES employees(employeeID) )";
    if($link->query($create_shift_table)){
        echo '<script> console.log("teamsemployees table created"); </script>';
    }
    else {
        echo '<script> console.log("failed to create teamemployees table); </script>';
    }
    $link->close();
}

function makeTeamShiftTable() {
    $link = new mysqli($_SERVER['RDS_HOSTNAME'], $_SERVER['RDS_USERNAME'], $_SERVER['RDS_PASSWORD'], $_SERVER['RDS_DB_NAME'], $_SERVER['RDS_PORT']);
    if (!$link) {
        echo '<script> console.log("Connection Failed: ' . mysqli_connect_error() . '"); </script>';
        die("Connection failed: " . mysqli_connect_error());
    }
    $create_shift_table = "CREATE TABLE IF NOT EXISTS teamshifts (
    teamName VARCHAR(150),
    shiftID INT(255) NOT NULL FOREIGN KEY REFERENCES shifts(shiftID) )";
    if($link->query($create_shift_table)){
        echo '<script> console.log("teamshifts table created"); </script>';
    }
    else {
        echo '<script> console.log("failed to create teamshifts table); </script>';
    }
    $link->close();
}

function signup($fname, $lname, $email, $password, $admin) {
    $success = false;
    $feedback = '<script> console.log("\nFirst Name: ' . $fname
    . '\nLast Name: ' . $lname . '\nEmail: ' . $email . '\nPassword: ' . $password . '"); </script>';
    $link = new mysqli($_SERVER['RDS_HOSTNAME'], $_SERVER['RDS_USERNAME'], $_SERVER['RDS_PASSWORD'], $_SERVER['RDS_DB_NAME'], $_SERVER['RDS_PORT']);
    if (!$link) {
        $feedback .= '<script> console.log("Connection Failed: ' . mysqli_connect_error() . '"); </script>';
        die("Connection failed: " . mysqli_connect_error());
    }
    else{
        $stmt = $link->prepare("INSERT INTO employees (firstname, lastname, email, password, admin) VALUES (?, ?, ?, ?, ?)");
        if($admin){
            $admin = 1;
        }
        else{
            $admin = 0;
        }
        $stmt->bind_param("ssssi", $fname, $lname, $email, $password, $admin);
        $success = $stmt->execute();
        if($success){
            $feedback .= '<script> console.log("successfully added employee"); </script>';
        }
        else {
            $feedback .= '<script> console.log("failed to add employee"); </script>';
        }
        $stmt->close();
        $link->close();
    }
    return $feedback;
}
function emailTaken($email){
    $link = new mysqli($_SERVER['RDS_HOSTNAME'], $_SERVER['RDS_USERNAME'], $_SERVER['RDS_PASSWORD'], $_SERVER['RDS_DB_NAME'], $_SERVER['RDS_PORT']);
    if (!$link) {
        echo '<script> console.log("Connection Failed: ' . mysqli_connect_error() . '"); </script>';
        die("Connection failed: " . mysqli_connect_error());
    }
    $stmt = $link->prepare("SELECT * FROM employees WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_row();
    $result->close();
    $stmt->close();
    $link->close();
    return $row != NULL;
}
function login($email, $password){
    $link = new mysqli($_SERVER['RDS_HOSTNAME'], $_SERVER['RDS_USERNAME'], $_SERVER['RDS_PASSWORD'], $_SERVER['RDS_DB_NAME'], $_SERVER['RDS_PORT']);
    if (!$link) {
        echo '<script> console.log("Connection Failed: ' . mysqli_connect_error() . '"); </script>';
        die("Connection failed: " . mysqli_connect_error());
    }
    $stmt = $link->prepare("SELECT * FROM employees WHERE email = ? AND password = ?");
    $stmt->bind_param("ss", $email, $password);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_row();
    $result->close();
    $stmt->close();
    $link->close();
    return $row != NULL;
}
function admin($email, $password) {
    $link = new mysqli($_SERVER['RDS_HOSTNAME'], $_SERVER['RDS_USERNAME'], $_SERVER['RDS_PASSWORD'], $_SERVER['RDS_DB_NAME'], $_SERVER['RDS_PORT']);
    if (!$link) {
        echo '<script> console.log("Connection Failed: ' . mysqli_connect_error() . '"); </script>';
        die("Connection failed: " . mysqli_connect_error());
    }
    $stmt = $link->prepare("SELECT * FROM employees WHERE email = ? AND password = ? AND admin = 1");
    $stmt->bind_param("ss", $email, $password);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_row();
    $result->close();
    $stmt->close();
    $link->close();
    return $row != NULL;
}

//TODO Delete this function when testing is over
function clearTables(){
    $link = new mysqli($_SERVER['RDS_HOSTNAME'], $_SERVER['RDS_USERNAME'], $_SERVER['RDS_PASSWORD'], $_SERVER['RDS_DB_NAME'], $_SERVER['RDS_PORT']);
    if (!$link) {
        echo '<script> console.log("Connection Failed: ' . mysqli_connect_error() . '"); </script>';
        die("Connection failed: " . mysqli_connect_error());
    }
    $drop_employee_table = "DROP TABLE IF EXISTS employees";
    if($link->query($drop_employee_table)){
        echo '<script> console.log("employees table created"); </script>';
    }
    else {
        echo '<script> console.log("failed to create employees table); </script>';
    }
    $link->close();
    makeEmployeeTable();
}