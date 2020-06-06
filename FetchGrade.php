<?php
    // method not allowed
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        die();
    }

    // bad request
    if (!isset($_POST['term'])) {
        http_response_code(400);
        die();
    }

    $config;

    try {
        require_once('../include/Initializer.php');
        $init = new Initializer();

        $config = $init->get_config();
        $init->verify_session();
    } catch (InitializerConfigInvalid $e) {
        // server error, invalid config
        http_response_code(500);
        die();
    } catch (InitializerInvalidSession $e) {
        // invalid session, 401 unauth
        http_response_code(401);
        die();
    }

    require_once('../object/CURL.php');
    require_once('../object/Page.php');
    
    $tmp_path = $_SESSION['session_file'];
    $curl = new CURL($config['general']['main_url'], $tmp_path);

    $curl->set_post(array('term_in' => $_POST['term']));
    $result = $curl->get_page('/banprod/bwskogrd.P_ViewGrde');

    $page = new Page($result);

    $data_tables = $page->query('//table[@class = "datadisplaytable"]');

    $result = array(
        'grades' => array(),
        'gpa' => array()
    );

    $grade_table = $data_tables->item(1);
    $grade_rows = $page->query('.//tr', $grade_table);
    for ($i = 1; $i < $grade_rows->length; $i++) {
        $cells = $page->query('.//td', $grade_rows->item($i));

        $result_add = array(
            'subj' => $cells->item(1)->textContent,
            'course' => $cells->item(2)->textContent,
            'section' => $cells->item(3)->textContent,
            'grade' => $cells->item(6)->textContent,
            'hours' => $cells->item(11)->textContent
        );

        array_push($result['grades'], $result_add);
    }

    $gpa_table = $data_tables->item(2);
    $gpa_rows = $page->query('.//tr', $gpa_table);
    for ($i = 1; $i < $gpa_rows->length; $i++) {
        $cells = $page->query('.//td', $gpa_rows->item($i));

        $result_add = array(
            'attempt' => $cells->item(0)->textContent,
            'earned' => $cells->item(1)->textContent,
            'hours' => $cells->item(2)->textContent,
            'quality' => $cells->item(3)->textContent,
            'gpa' => $cells->item(4)->textContent
        );

        array_push($result['gpa'], $result_add);
    }

    http_response_code(200);
    echo(json_encode($result));
?>