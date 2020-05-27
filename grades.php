<?php
    require_once('include/Initializer.php');
    require_once('include/ViewManager.php');
    
    // view manager
    $vm;
    $config;
    
    try {
        $init = new Initializer();
        $vm = new ViewManager();
    
        $config = $init->get_config();
        $init->verify_session();
    } catch (InitializerConfigInvalid $e) {
        die('> invalid config');
    } catch (InitializerInvalidSession $e) {
        header('Location: /');
        die();
    }

    $vm->term = $_SESSION['term'];
    $vm->render('grades.php', true);
?>