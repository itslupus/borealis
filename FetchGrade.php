<?php
    /* ===========================================================
    ||  [Fetches grades based on term, includes term and overall]
    ||  PHP     7.2.24
    || 
    ||  POST    /api/FetchGrade.php
    ||
    ||  PARAMS  term: string
    ||
    ||  RETURN  {
    ||              result: {
    ||                  grades: [
    ||                      {
    ||                          subj: COMP
    ||                          course: 3010
    ||                          section: A01
    ||                          grade: A
    ||                          hours: 3.00
    ||                      },
    ||                      ......
    ||                  ],
    ||                  gpa: [
    ||                      {
    ||                          attempt: 12.00
    ||                          earned: 12.00
    ||      [TERM GPA]          hours: 12.00
    ||                          quality: 42.00
    ||                          gpa: 4.00
    ||                      },
    ||                      {
    ||                          attempt: 12.00
    ||                          earned: 12.00
    ||   [CUMULATIVE GPA]       hours: 12.00
    ||                          quality: 42.00
    ||                          gpa: 4.00
    ||                      },
    ||                      {
    ||                          attempt: 12.00
    ||                          earned: 12.00
    ||    [TRANSFER GPA]        hours: 12.00
    ||                          quality: 42.00
    ||                          gpa: 4.00
    ||                      },
    ||                      {
    ||                          attempt: 12.00
    ||                          earned: 12.00
    ||      [TOTAL GPA]         hours: 12.00
    ||                          quality: 42.00
    ||                          gpa: 4.00
    ||                      }
    ||                  ]
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

    if (!isset($_COOKIE['token']) || !isset($_POST['term'])) {
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
    $curl->set_post(array('term_in' => $_POST['term']));
    $result = $curl->get_page('/banprod/bwskogrd.P_ViewGrde');

    $page = new Page($result);

    $data_tables = $page->query('//table[@class = "datadisplaytable"]');

    $result = array(
        'result' => array(
            'grades' => array(),
            'gpa' => array()
        )
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

        array_push($result['result']['grades'], $result_add);
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

        array_push($result['result']['gpa'], $result_add);
    }

    header('Content-Type: text/json');
    http_response_code(200);
    echo(json_encode($result));
?>