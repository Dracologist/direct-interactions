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
        $f3->set('no_email', false);
        $f3->set('no_password', false);
        $f3->set('wrong_email', false);
        $f3->set('wrong_password', false);
        if($_SESSION['logged-in'] == true){
            $f3->reroute("/");
        }
        else{
            echo Template::instance()->render('html/login.html');
        }
    });
        $f3->route('POST /login', function($f3) {
            $error = false;
            $f3->set('no_email', false);
            $f3->set('no_password', false);
            $f3->set('wrong_email', false);
            $f3->set('wrong_password', false);
            if(!isset($_POST['email'])){
                $f3->set('no_email', true);
                $error = true;
            }
            if(!isset($_POST['password'])){
                $f3->set('no_password', true);
                $error = true;
            }
            if(!$error){
                $_SESSION['logged-in'] = login($_POST['email'], sha1($_POST['password']))
                //TODO Disable test credentials by deleting next line
                || (($_POST['email'] == "user" || $_POST['email'] == "admin") && $_POST['password'] == "password");
                
                $_SESSION['admin'] = admin($_POST['email'], sha1($_POST['password'])) 
                //TODO Disable test credentials by deleting next line
                || ($_SESSION['logged-in'] && $_POST['email'] == "admin");
            }
            if(!$_SESSION['logged-in']){
                if(!emailTaken($_POST['email'])){
                    $f3->set('wrong_email', true);
                }
                else {
                    $f3->set('wrong_password', true);
                }
                echo Template::instance()->render('html/login.html');
            }
            else{
                $f3->reroute("/");
            }
        });
    $f3->route('GET|POST /logout', function($f3) {
        session_destroy();
        $f3->reroute("/");
    });
    //TODO Remove this route once testing is done
    $f3->route('GET|POST /clear-tables', function($f3) {
        clearTables();
        $f3->reroute("/");
    });
    $f3->route('GET /signup', function($f3) {
        $f3->set('email_error', false);
        $f3->set('fname_error', false);
        $f3->set('lname_error', false);
        $f3->set('password_error', false);
        $f3->set('retype_password_error', false);
        $f3->set('unique_email_error', false);
        echo Template::instance()->render('html/signup.html');
    });
    $f3->route('POST /signup', function($f3, $pdo) {
        $error = false;
        $f3->set('email_error', false);
        $f3->set('fname_error', false);
        $f3->set('lname_error', false);
        $f3->set('password_error', false);
        $f3->set('retype_password_error', false);
        $f3->set('unique_email_error', false);
        //email validation
        if(!isset($_POST['email']) || strlen($_POST['email']) < 1){
            $f3->set('email_error', true);
            $error = true;
        }
        elseif (emailTaken($_POST['email'])){
            $f3->set('unique_email_error', true);
            $error = true;
        }
        //first name validation
        if(!isset($_POST['fname']) || strlen($_POST['fname']) < 1){
            $f3->set('fname_error', true);
            $error = true;
        }
        //last name validation
        if(!isset($_POST['lname'])  || strlen($_POST['lname']) < 1){
            $f3->set('lname_error', true);
            $error = true;
        }
        //password validation
        if(!isset($_POST['password']) || strlen($_POST['password']) < 1){
            $f3->set('password_error', true);
            $error = true;
        }
        //password retype validation
        if($_POST['retype-password'] != $_POST['password']){
            $f3->set('retype_password_error', true);
            $error = true;
        }
        //if there's an error, show the signup sheet again
        if(!$error){ 
            echo signup($_POST['fname'], $_POST['lname'], $_POST['email'], sha1($_POST['password']), $_POST['admin']);
            $f3->reroute("/");
        }
        //otherwise, submit the user to the database and go to the home page
        else{
            echo Template::instance()->render('html/signup.html');
        }
    });
    $f3->run();
?>