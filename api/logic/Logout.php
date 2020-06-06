<?php
    require_once('../include/Initializer.php');

    $init;
    try {
        $init = new Initializer();
        $init->start_session();
    } catch (InitializerConfigInvalid $e) {
        die ($e);
    }

    $init->destroy_session();

    header('Location: /');
    die();
?>