<?php 
    session_start();
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    require_once('vendor/autoload.php');
    require('database-functions.php');
    setup();
    $f3 = Base::instance();
    $f3->set('ONERROR',
        function($f3) {
            echo $f3->get('ERROR.text');
    });
    $f3->route('GET|POST /', function($f3) {
        if($_SESSION['logged-in'] == true){
            echo Template::instance()->render('html/home.html');
        }
        else {
            $f3->reroute("/login");
        }
    });
    $f3->route('GET /login', function($f3) {
        $f3->set('no-email', false);
        $f3->set('no-password', false);
        if($_SESSION['logged-in'] == true){
            $f3->reroute("/");
        }
        else{
            echo Template::instance()->render('html/login.html');
        }
    });
        $f3->route('POST /login', function($f3) {
            $error = false;
            $f3->set('no-email', false);
            $f3->set('no-password', false);
            if(!isset($_POST['email'])){
                $f3->set('no-email', true);
                $error = true;
            }
            if(!isset($_POST['password'])){
                $f3->set('no-password', true);
                $error = true;
            }
            if(!$error){
                
                $_SESSION['logged-in'] = login($_POST['email'], $_POST['password']) 
                //TODO Disable test credentials by deleting next line
                || (($_POST['email'] == "user" || $_POST['email'] == "admin") && $_POST['password'] == "password");
                
                $_SESSION['admin'] = admin($_POST['email'], $_POST['password']) 
                //TODO Disable test credentials by deleting next line
                || ($_SESSION['logged-in'] && $_POST['email'] == "admin");
                
            }
            if(!$_SESSION['logged-in']){
                echo Template::instance()->render('html/login.html');
            }
        });
    $f3->route('GET|POST /logout', function($f3) {
        session_destroy();
        $f3->reroute("/");
    });
    $f3->route('GET /signup', function($f3) {
        echo Template::instance()->render('html/signup.html');
    });
    $f3->route('POST /signup', function($f3, $pdo) {
        $error = false;
        $f3->set('email-error', false);
        $f3->set('fname-error', false);
        $f3->set('lname-error', false);
        $f3->set('password-error', false);
        $f3->set('retype-password-error', false);
        $f3->set('unique-email-error', false);
        //email validation
        if(!isset($_POST['email'])){
            $f3->set('email-error', true);
            $error = true;
        }
        elseif (emailTaken($_POST['email'])){
            $f3->set('unique-email-error', true);
            $error = true;
        }
        //first name validation
        if(!isset($_POST['fname'])){
            $f3->set('fname-error', true);
            $error = true;
        }
        //last name validation
        if(!isset($_POST['lname'])){
            $f3->set('lname-error', true);
            $error = true;
        }
        //password validation
        if(!isset($_POST['password'])){
            $f3->set('password-error', true);
            $error = true;
        }
        //password retype validation
        if($_POST['retype-password'] != $_POST['password']){
            $f3->set('retype-password-error', true);
            $error = true;
        }
        //if there's an error, show the signup sheet again
        if($error){
            echo Template::instance()->render('html/signup.html');
        }
        //otherwise, submit the user to the database and go to the home page
        else{
            signup($_POST['fname'], $_POST['lname'], $_POST['email'], sha1($_POST['password']), $_POST['admin']);
            $f3->reroute("/");
        }
    });
    $f3->run();
?>