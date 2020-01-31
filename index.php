<?php 
    session_start();
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    require_once('vendor/autoload.php');
    $f3 = Base::instance();
    $f3->route('GET|POST /', function($f3) {
            f3->set('title', "Home");
            echo Template::instance()->render('html/head.html');
            echo Template::instance()->render('html/test-home.html');
        });
    $f3->route('GET /login', function($f3) {
        f3->set('title', "Log In");
        echo Template::instance()->render('html/head.html');
        echo Template::instance()->render('html/login.html');
    });
    $f3->route('POST /login', function($f3) {
        $_SESSION['logged-in'] = TRUE;
        $f3->reroute("/home");
    });
    $f3->route('GET|POST /home', function($f3) {
        f3->set('title', "Home Page");
        echo Template::instance()->render('html/head.html');
        echo Template::instance()->render('html/home.html');
    });
    $f3->run();
?>