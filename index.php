<?php
    require_once('logic/Loader.php');

    try {
        $loader = new Loader();

        $loader->read_init();
        $loader->init_session();
    } catch (LoaderMissingIniSection $e) {
        echo('missing ini section');
        die();
    }

    echo('hello world');
?>