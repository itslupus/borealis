<?php
    require_once($_SERVER['DOCUMENT_ROOT'] . '/api/include/MrManager.php');

    require_once($_SERVER['DOCUMENT_ROOT'] . '/api/object/CURL.php');
    require_once($_SERVER['DOCUMENT_ROOT'] . '/api/object/Token.php');
    require_once($_SERVER['DOCUMENT_ROOT'] . '/api/object/Page.php');

    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        // 405 method not allowed
        http_response_code(405);
        die();
    }

    if (!isset($_GET['token']) || !isset($_GET['term'])) {
        // 400 bad request
        http_response_code(400);
        die();
    }

    $manager = null;
    $config = null;
    $token = null;

    try {
        $manager = new MrManager();

        $config = $manager->get_config();

        $token = $manager->validate_token($_GET['token']);
        $token = $manager->regenerate_token($token);

        $manager->validate_banner_session($token);
    } catch (MrManagerInvalidConfig $e) {
        // 500 internal server error
        http_response_code(500);
        die();
    } catch (MrManagerInvalidToken $e2) {
        // 401 unauth
        http_response_code(401);
        die('invalid token');
    } catch (MrManagerInvalidBannerSession $e3) {
        // 401 unauth
        http_response_code(401);
        die('invalid banner');
    } catch (MrManagerExpiredToken $e4) {
        // 401 unauth
        http_response_code(401);
        die('expired token');
    }
    
    //TODO: un-hardcode cookie path
    $main_url = $config['general']['main_url'];
    $tmp_path = $_SERVER['DOCUMENT_ROOT'] . '/tmp/' . $token->get_tmp_file_name();
    $curl = new CURL($main_url, $tmp_path);

    $curl->set_post(array('term_in' => $_GET['term']));
    $result = $curl->get_page('/banprod/bwskogrd.P_ViewGrde');

    $page = new Page($result);

    $data_tables = $page->query('//table[@class = "datadisplaytable"]');

    $result = array(
        'token' => $token->get_token(),
        'grades' => array(),
        'gpa' => array()
    );

    $grade_table = $data_tables->item(1);
    $grade_rows = $page->query('.//tr', $grade_table);
    for ($i = 1; $i < $grade_rows->length; $i++) {
        $cells = $page->query('.//td', $grade_rows->item($i));

        $result_add = array(
            'subj' => trim($cells->item(1)->textContent),
            'course' => trim($cells->item(2)->textContent),
            'section' => trim($cells->item(3)->textContent),
            'grade' => trim($cells->item(6)->textContent),
            'hours' => trim($cells->item(11)->textContent)
        );

        array_push($result['grades'], $result_add);
    }

    $gpa_table = $data_tables->item(2);
    $gpa_rows = $page->query('.//tr', $gpa_table);
    for ($i = 1; $i < $gpa_rows->length; $i++) {
        $cells = $page->query('.//td', $gpa_rows->item($i));

        $result_add = array(
            'attempt' => trim($cells->item(0)->textContent),
            'earned' => trim($cells->item(1)->textContent),
            'hours' => trim($cells->item(2)->textContent),
            'quality' => trim($cells->item(3)->textContent),
            'gpa' => trim($cells->item(4)->textContent)
        );

        array_push($result['gpa'], $result_add);
    }

    http_response_code(200);
    echo(json_encode($result));
?>