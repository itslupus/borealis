<?php
    /* ===========================================================
    ||  [Fetches a course's space remaining and meet times/locations]
    ||  PHP     7.2.24
    || 
    ||  POST    /FetchCourse.php
    ||
    || === PARAMETERS ============================================
    ||  term
    ||  - the term to query the course
    ||  - eg.
    ||      term = "202090"
    ||
    ||  course_code
    ||  - the course to lookup
    ||  - eg.
    ||      course_code = "COMP 1010"
    ||
    || === RETURNS ===============================================
    ||  Example return data:
    ||
    ||  {
    ||      result: {
    ||          sections: {
    ||              A01: {
    ||                  crn: "10188",
    ||                  cap: "80",
    ||                  cnt: "80",
    ||                  rem: "0",
    ||                  "wl_cap": "999",
    ||                  "wl_cnt": "44",
    ||                  "wl_rem": "955",
    ||                  instr: "Christina M. Penner (P)",
    ||                  meets: [
    ||                      {
    ||                          days: "TR",
    ||                          time: "11:30 am-12:45pm",
    ||                          bounds: "09/09-12/11",
    ||                          location: "BUILDING 123"
    ||                      }
    ||                  ],
    ||                  desc: "This contains any information listed under this section"
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
        die('bad method');
    }

    if (!isset($_COOKIE['token']) || !isset($_POST['term']) || !isset($_POST['course_code'])) {
        // 400 bad request
        http_response_code(400);
        die('bad params');
    }

    $course_code = explode(' ', $_POST['course_code']);

    if (count($course_code) !== 2) {
        // 400 bad request
        http_response_code(400);
        die('bad course');
    }

    $manager = new MrManager($_COOKIE['token']);

    $data = array(
        'term_in' => $_POST['term'],
        'sel_subj' => ['dummy', $course_code[0]],
        'SEL_CRSE' => $course_code[1],
        'SEL_TITLE' => '',
        'BEGIN_HH' => '0',
        'BEGIN_MI' => '0',
        'BEGIN_AP' => 'a',
        'SEL_DAY' => 'dummy',
        'SEL_PTRM' => 'dummy',
        'END_HH' => '0',
        'END_MI' => '0',
        'END_AP' => 'a',
        'SEL_CAMP' => 'dummy',
        'SEL_SCHD' => 'dummy',
        'SEL_SESS' => 'dummy',
        'SEL_INSTR' => ['dummy', '%'],
        'SEL_ATTR' => ['dummy', '%'],
        'SEL_LEVL' => ['dummy', '%'],
        'SEL_INSM' => 'dummy',
        'sel_dunt_code' => '',
        'sel_dunt_unit' => '',
        'call_value_in' => '',
        'rsts' => 'dummy',
        'crn' => 'dummy',
        'path' => '1',
        'SUB_BTN' => 'View Sections'
    );
    $data = http_build_query($data);
    $data = preg_replace('/\%5B[0-9]\%5D/', '', $data);

    $page = $manager->get_page('/banprod/bwskfcls.P_GetCrse', $data);

    $rows = $page->query('//table[@class = "datadisplaytable"]/tr');

    $sections = array();

    $new_section = array();
    $curr_section = '';
    for ($i = 2; $i < $rows->length; $i++) {
        $row = $rows->item($i);
        $cells = $page->query('.//td', $row);

        if ($cells->length === 1) {
            $sections[$curr_section] = $new_section;

            $new_section = array();
            $curr_section = '';
        } else if ($cells->length === 2) {
            $new_section['desc'] = trim($cells->item(1)->textContent);
        } else if ($cells->length > 2) {
            if ($curr_section !== '') {
                array_push($new_section['meets'], array(
                    'days' => trim($cells->item(8)->textContent),
                    'time' => trim($cells->item(9)->textContent),
                    'bounds' => trim($cells->item(17)->textContent),
                    'location' => trim($cells->item(18)->textContent)
                ));
            } else {
                $curr_section = trim($cells->item(4)->textContent);

                $new_section['crn'] = trim($cells->item(1)->textContent);
    
                $new_section['cap'] = trim($cells->item(10)->textContent);
                $new_section['cnt'] = trim($cells->item(11)->textContent);
                $new_section['rem'] = trim($cells->item(12)->textContent);
    
                $new_section['wl_cap'] = trim($cells->item(13)->textContent);
                $new_section['wl_cnt'] = trim($cells->item(14)->textContent);
                $new_section['wl_rem'] = trim($cells->item(15)->textContent);
    
                $new_section['instr'] = trim($cells->item(16)->textContent);
    
                $new_section['meets'] = array(
                    array(
                        'days' => trim($cells->item(8)->textContent),
                        'time' => trim($cells->item(9)->textContent),
                        'bounds' => trim($cells->item(17)->textContent),
                        'location' => trim($cells->item(18)->textContent)
                    )
                );
            }
        }
    }

    $result = array(
        'result' => array('sections' => $sections)
    );

    header('Content-Type: text/json');
    http_response_code(200);
    echo(json_encode($result));
?>