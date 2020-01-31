<?php 
    session_start();
    require_once('vendor/autoload.php');
    $f3 = Base::instance();
    //please work
    $f3->route('GET|POST /',
        function($f3) {
            f3->set('title', "Home");
            if ($_SESSION['logged-in'] != TRUE) {
                $f3->reroute("/login");
            }
            echo Template::instance()->render('html/head.html');
            echo Template::instance()->render('html/home.html');
        }
        );
    $f3->route('GET /login', function($f3) {
        f3->set('title', "Log In");
        echo Template::instance()->render('html/head.html');
        echo Template::instance()->render('html/login.html');
    });
    $f3->route('POST /login', function($f3) {
        $_SESSION['logged-in'] = TRUE;
        $f3->reroute("/");
    });
    $f3->run();
?>