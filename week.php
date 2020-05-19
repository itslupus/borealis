<?php
    require_once('include/Initializer.php');
    require_once('include/ViewManager.php');

    require_once('logic/FetchWeek.php');
    require_once('logic/FetchAccSummary.php');

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

    $courses = fetch_week($config, $_SESSION['term']);
    $vm->courses = $courses;

    $vm->render('week.php', true);
?>