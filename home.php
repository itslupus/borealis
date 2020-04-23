<?php
    require_once('include/Initializer.php');
    require_once('include/ViewManager.php');
    require_once('object/CURL.php');
    require_once('object/Page.php');

    // view manager
    $vm;
    $config;

    try {
        $init = new Initializer();
        $vm = new ViewManager();

        $config = $init->read_config();
        $init->verify_session();
    } catch (InitializerConfigInvalid $e) {
        die('> invalid config');
    } catch (InitializerInvalidSession $e) {
        header('Location: /');
        die();
    }

    $tmp_path = $_SESSION['session_file'];
    $curl = new CURL($config['general']['main_url'], $tmp_path);

    $post_params = array('term_in' => 202010);
    $curl->set_post($post_params);
    $response = $curl->get_page('/banprod/bwskfshd.P_CrseSchdDetl');

    $crse_page = new Page($response);
    $el = $crse_page->get_elements_by_class('div', 'datadisplaytable');

    $vm->name = $_SESSION['name'];
    $vm->el = $el->length;
    $vm->render('home.php', true);
?>