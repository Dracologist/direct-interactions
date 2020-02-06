<?php 
    session_start();
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    require_once('vendor/autoload.php');
    require('database-setup.php');
    $f3 = Base::instance();
    $f3->set('ONERROR',
        function($f3) {
            echo $f3->get('ERROR.text');
    });
    $f3->route('GET|POST /', function($f3) {
        if($_SESSION['logged-in'] == TRUE){
            echo Template::instance()->render('html/home.html');
        }
        else {
            $f3->reroute("/login");
        }
    });
    $f3->route('GET|POST /login', function($f3) {
        if($_POST['username'] == "admin" && $_POST['password'] == "password"){
            $_SESSION['logged-in'] = TRUE;
            $_SESSION['admin'] = TRUE;
            $f3->reroute("/");
        }
        elseif ($_POST['username'] == "user" && $_POST['password'] == "password")
        {
            $_SESSION['logged-in'] = TRUE;
            $f3->reroute("/");
        }
        else{
            echo Template::instance()->render('html/login.html');
        }
    });
    $f3->route('GET|POST /logout', function($f3) {
        $_SESSION['logged-in'] = FALSE;
        $_SESSION['admin'] = FALSE;
        $f3->reroute("/");
    });
        $f3->route('GET|POST /signup', function($f3) {
            echo Template::instance()->render('html/signup.html');
        });
    $f3->run();
?>