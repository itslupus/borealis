<?php
    require_once('include/Initializer.php');
    require_once('include/ViewManager.php');

    // view manager
    $vm;

    try {
        $init = new Initializer();
        $vm = new ViewManager();

        $init->verify_session();

        header('Location: /home.php');
        die();
    } catch (InitializerConfigInvalid $e) {
        die('> invalid config');
    } catch (InitializerInvalidSession $e) {
        $vm->render('index.php', true);
    }
?>