<?php
    // method not allowed
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        die();
    }

    // bad request
    if (!isset($_POST['course_code']) || !isset($_POST['term'])) {
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

    $course_code = explode(' ', $_POST['course_code']);

    // bad request
    if (count($course_code) !== 2) {
        http_response_code(400);
        die();
    }

    require_once('../object/CURL.php');
    require_once('../object/Page.php');
    
    $tmp_path = $_SESSION['session_file'];
    $curl = new CURL($config['general']['main_url'], $tmp_path);
    
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

    $curl->set_post($data);
    $result = $curl->get_page('/banprod/bwskfcls.P_GetCrse');

    $page = new Page($result);

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

    $sections = json_encode($sections);

    http_response_code(200);
    echo($sections);
?>