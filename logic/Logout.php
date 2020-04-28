<?php
    require_once('../include/Initializer.php');

    // note that this will not destroy sessions that ended due to inactivity
    unlink($_SESSION['session_file']);

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