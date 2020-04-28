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

        $config = $init->get_config();
        $init->verify_session();
    } catch (InitializerConfigInvalid $e) {
        die('> invalid config');
    } catch (InitializerInvalidSession $e) {
        header('Location: /');
        die();
    }

    // get the session file and create curl instance
    $tmp_path = $_SESSION['session_file'];
    $curl = new CURL($config['general']['main_url'], $tmp_path);

    // fetch course information for winter 2020 term
    //        AAAABB
    // year --^^^^
    //            ^^-- term (90 => fall, 10 => winter, 40 => spring/summer)
    $post_params = array('term_in' => 202010);
    $curl->set_post($post_params);
    $response = $curl->get_page('/banprod/bwskfshd.P_CrseSchdDetl');

    // get the datadisplaytable
    $crse_page = new Page($response);
    $tables = $crse_page->get_elements_by_class('table', 'datadisplaytable');

    // these are pairs of tables
    // first one contains course info, second one contains meet times
    $courses = array();
    for ($i = 0; $i < $tables->length; $i += 2) {
        $details = array();

        $course_info = $tables->item($i);
        $course_times = $tables->item($i + 1);

        // get course name from first table
        $course_name = $crse_page->get_elements_by_tag('caption', $course_info);

        // get instructor info from first table children
        $course_info_detail = $crse_page->get_elements_by_tag('td', $course_info);
        $course_instr_name = trim($course_info_detail->item(3)->textContent);

        array_push($details, $course_instr_name);

        // find the meet times (its a single row) from second table
        $meet_time_els = $crse_page->get_last_elements('td', $course_times);
        foreach ($meet_time_els as $el) {
            array_push($details, $el->textContent);
        }

        $courses[$course_name->item(0)->textContent] = $details;
    }

    $vm->name = $_SESSION['name'];
    $vm->courses = $courses;
    $vm->render('home.php', true);
?>