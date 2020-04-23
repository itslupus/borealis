<?php
    require_once('../include/Initializer.php');

    $init = new Initializer();
    $init->start_session();
    $init->destroy_session();

    header('Location: /');
    die();
?>