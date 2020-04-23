<?php
    require_once('include/Initializer.php');
    require_once('include/ViewManager.php');

    // view manager
    $vm;

    try {
        $init = new Initializer();
        $vm = new ViewManager();

        $init->verify_session();

        $vm->render('home.php', true);
    } catch (InitializerConfigInvalid $e) {
        die('> invalid config');
    } catch (InitializerInvalidSession $e) {
        header('Location: /');
        die();
    }
?>