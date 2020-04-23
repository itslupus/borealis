<?php
    require_once('../include/Initializer.php');

    $init = new Initializer();
    $init->start_session();

    // note that this will not destroy sessions that ended due to inactivity
    unlink($_SESSION['session_file']);

    $init->destroy_session();

    header('Location: /');
    die();
?>