<?php
    /* ===========================================================
    ||  [Fetches the courses from subjects that are currently listed in the term]
    ||  PHP     7.2.24
    || 
    ||  POST    /FetchSubjectCourses.php
    ||
    || === PARAMETERS ============================================
    ||  term
    ||  - the term to query the currently offered courses
    ||  - eg.
    ||      term = "202090"
    ||
    ||  subjects
    ||  - an array of subjects to serch
    ||  - eg.
    ||      subjects[] = "COMP"
    ||      subjects[] = "CHEM"
    ||
    || === RETURNS ===============================================
    ||  Example return data:
    ||
    ||  {
    ||      result: {
    ||          subjects: {
    ||              "Computer Science": {
    ||                  "COMP 1010": "COMP 1010 Introductory Computer Science 1 3.00CR"
    ||              },
    ||              "Chemistry": {
    ||                  "COMP 1300": "Whatever this course is called 3.00CR"
    ||              }
    ||          }
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

    if (!isset($_COOKIE['token']) || !isset($_POST['term']) || !isset($_POST['subjects'])) {
        // 400 bad request
        http_response_code(400);
        die();
    }

    $manager = new MrManager($_COOKIE['token']);
    
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

    $page = $manager->get_page('/banprod/bwskfcls.P_GetCrse', $data);
    
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