<?php
    /* ===========================================================
    ||  [Fetches the courses from subjects that are currently listed in the term]
    ||  PHP     7.2.24
    || 
    ||  POST    /api/FetchSubjectCourses.php
    ||
    ||  RETURN  {
    ||              result: {
    ||                  subjects: {
    ||                      Computer Science: {
    ||                          COMP 1010: COMP 1010 Introductory Computer Science 1 3.00CR,
    ||                          ......
    ||                      },
    ||                      .......
    ||                  }
    ||              }
    ||          }
    || ======================================================== */
    
    require_once($_SERVER['DOCUMENT_ROOT'] . '/api/include/MrManager.php');

    require_once($_SERVER['DOCUMENT_ROOT'] . '/api/object/CURL.php');
    require_once($_SERVER['DOCUMENT_ROOT'] . '/api/object/Token.php');
    require_once($_SERVER['DOCUMENT_ROOT'] . '/api/object/Page.php');

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        // 405 method not allowed
        http_response_code(405);
        die();
    }

    if (!isset($_COOKIE['token']) || !isset($_POST['term']) || !isset($_POST['subjects'])) {
        // 400 bad request
        http_response_code(400);
        die();
    }

    $manager = new MrManager();
    $config = $manager->get_config();
    $token = null;

    try {
        $token = $manager->validate_token($_COOKIE['token']);
        $token = $manager->regenerate_token($token);

        $manager->validate_banner_session($token);
        $manager->set_token_cookie($token);
    } catch (MrManagerInvalidToken $e) {
        // 401 unauth
        http_response_code(401);
        die('invalid token');
    } catch (MrManagerInvalidBannerSession $e2) {
        // 401 unauth
        http_response_code(401);
        die('invalid banner');
    } catch (MrManagerExpiredToken $e3) {
        // 401 unauth
        http_response_code(401);
        die('expired token');
    }
    
    $curl = $manager->get_curl_object($token->get_tmp_file_name());

    $data = array(
        'rsts' => 'dummy',
        'crn' => 'dummy',
        'term_in' => $_POST['term'],
        'sel_day' => 'dummy',
        'sel_schd' => 'dummy',
        'sel_insm' => 'dummy',
        'sel_camp' => 'dummy',
        'sel_levl' => 'dummy',
        'sel_sess' => 'dummy',
        'sel_instr' => 'dummy',
        'sel_ptrm' => ['dummy', '%'],
        'sel_attr' => 'dummy',
        'sel_crse' => '',
        'sel_title' => '',
        'sel_from_cred' => '',
        'sel_to_cred' => '',
        'begin_hh' => '0',
        'begin_mi' => '0',
        'end_hh' => '0',
        'end_mi' => '0',
        'begin_ap' => 'x',
        'end_ap' => 'y',
        'path' => '1',
        'SUB_BTN' => 'Course Search'
    );

    $data['sel_subj'] = array('dummy');
    foreach ($_POST['subjects'] as $subj) {
        if ($subj != null && $subj != '' && strlen($subj) <= 4) {
            array_push($data['sel_subj'], $subj);
        }
    }

    $data = http_build_query($data);
    $data = preg_replace('/\%5B[0-9]\%5D/', '', $data);

    $curl->set_post($data);
    $result = $curl->get_page('/banprod/bwskfcls.P_GetCrse');

    $page = new Page($result);
    $crse_tables = $page->query('//table[@class = "datadisplaytable"]');

    $display_subjs = array();
    for ($i = 0; $i < $crse_tables->length; $i++) {
        $table = $crse_tables->item($i);

        // first tr will be term (summer 2020 for example)
        // second tr will be subj name
        $subj_info = $page->query('.//tr', $table);

        // for some reason, aurora (or maybe something in the middle) does not enclose <td> inside of a <tr>
        $course_info = $page->query('.//td[@class = "dddefault"]', $table);

        if ($course_info->length === 0) continue;
            
        $build_crses = array();
        $build_string = '';
        for ($k = 0; $k < $course_info->length; $k++) {
            if ($k % 4 === 0 && $k != 0) {
                $subj_code = trim($course_info->item($k - 4)->textContent);
                $subj_num = trim($course_info->item($k - 3)->textContent);
                $course_code = $subj_code . ' ' . $subj_num;

                $build_crses[$course_code] = trim($build_string);

                $build_string = '';
            }

            $td = $course_info->item($k);
            $build_string .= trim($td->textContent) . ' ';
        }

        $subj_name = trim($subj_info->item(1)->textContent);
        $display_subjs[$subj_name] = $build_crses;
    }

    $result = array(
        'result' => array('subjects' => $display_subjs)
    );

    header('Content-Type: text/json');
    http_response_code(200);
    echo(json_encode($result));
?>