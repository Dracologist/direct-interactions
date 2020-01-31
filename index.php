<?php 
    require_once('vendor/autoload.php');
    $f3 = Base::instance();
    $f3->route('GET /',
        function($f3) {
            $f3->set('name','world');
            echo Template::instance()->render('htlm/head.html');
            echo Template::instance()->render('htlm/test-home.html');
        }
        );
    $f3->run();
?>