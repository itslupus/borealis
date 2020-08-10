<?php
    /* ===========================================================
    ||  [Fetches grades based on term, includes term and overall]
    ||  PHP     7.2.24
    || 
    ||  POST    /FetchGrade.php
    ||
    || === NOTES =================================================
    ||  The GPA data return will contain 4 groups:
    ||  - the first group is TERM GPA
    ||  - second is CUMULATIVE GPA
    ||  - third is TRANSFERED GPA
    ||  - fourth is TOTAL GPA
    ||
    || === PARAMETERS ============================================
    ||  term
    ||  - the term to query for submitted grades
    ||  - eg.
    ||      term = "202090"
    ||
    || === RETURNS ===============================================
    ||  Example return data:
    ||
    ||  {
    ||      result: {
    ||          grades: [
    ||              {
    ||                  subj: COMP,
    ||                  course: 1010,
    ||                  section: A01,
    ||                  grade: A+,
    ||                  hours: "3.00"
    ||              }
    ||          ],
    ||          gpa: [
    ||              {
    ||                  attempt: "12.00",
    ||                  earned: "12.00",
    ||                  hours: "12.00",
    ||                  quality: "12.00",
    ||                  gpa: "4.00",
    ||              },
    ||              {
    ||                  attempt: "12.00",
    ||                  earned: "12.00",
    ||                  hours: "12.00",
    ||                  quality: "12.00",
    ||                  gpa: "4.00",
    ||              },
    ||              {
    ||                  attempt: "12.00",
    ||                  earned: "12.00",
    ||                  hours: "12.00",
    ||                  quality: "12.00",
    ||                  gpa: "4.00",
    ||              },
    ||              {
    ||                  attempt: "12.00",
    ||                  earned: "12.00",
    ||                  hours: "12.00",
    ||                  quality: "12.00",
    ||                  gpa: "4.00",
    ||              }
    ||          ]
    ||      }
    ||  }
    || ======================================================== */

    require_once(__DIR__ . '/include/MrManager.php');
    require_once(__DIR__ . '/object/Page.php');

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

    $manager = new MrManager($_COOKIE['token']);
    
    $page = $manager->get_page('/banprod/bwskogrd.P_ViewGrde', array('term_in' => $_POST['term']));

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