<?php 
    require_once('vendor/autoload.php');
    $f3 = Base::instance();
    $f3->route('GET /',
        function($f3) {
            echo Template::instance()->render('html/head.html');
            echo Template::instance()->render('html/test-home.html');
        }
        );
    $f3->run();
?>