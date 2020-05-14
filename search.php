<?php
    require_once('include/Initializer.php');
    require_once('include/ViewManager.php');
    // require_once('object/CURL.php');
    // require_once('object/Page.php');
    
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

    $method = $_SERVER['REQUEST_METHOD'];
    if ($method === 'GET') {
        $courses = array(
            'ACC' => 'Accounting',
            'ABIZ' => 'AgBusiness and AgEconomics',
            'AGRI' => 'Agriculture',
            'ANSC' => 'Animal Science',
            'ANTH' => 'Anthropology',
            'ARCG' => 'Architecture Interdisciplinary',
            'ARTS' => 'Arts Interdisciplinary',
            'ASTR' => 'Astronomy',
            'BGEN' => 'Biochem. and Medical Genetics',
            'BIOL' => 'Biological Sciences',
            'BME' => 'Biomedical Engineering',
            'BIOE' => 'Biosystems Engineering',
            'CDSB' => 'Canadian Studies St. Boniface',
            'CATH' => 'Catholic Studies',
            'CHEM' => 'Chemistry',
            'CIVL' => 'Civil Engineering',
            'CLAS' => 'Classical Studies',
            'CHSC' => 'Community Health Sciences',
            'COMP' => 'Computer Science',
            'DENT' => 'Dentistry',
            'ECON' => 'Economics',
            'EDUA' => 'Education Admin, Fndns & Psych',
            'EDUB' => 'Education Curric, Tchg, & Lrng',
            'ECE' => 'Electr. and Computer Engin.',
            'ENG' => 'Engineering',
            'ENGL' => 'English',
            'ENTR' => 'Entrepreneurship/Small Bus.',
            'ENVR' => 'Environment',
            'EER' => 'Environment, Earth & Resources',
            'EVDS' => 'Environmental Design',
            'EVIE' => 'Environmental Interior Environ',
            'FMLY' => 'Family Social Sciences',
            'FILM' => 'Film Studies',
            'FIN' => 'Finance',
            'FAAH' => 'Fine Art, Art History Courses',
            'FA' => 'Fine Art, General Courses',
            'FRAN' => 'Francais St. Boniface',
            'FREN' => 'French',
            'GMGT' => 'General Management',
            'GEOG' => 'Geography',
            'GEOL' => 'Geological Sciences',
            'GRMN' => 'German',
            'GRAD' => 'Graduate Studies',
            'HEAL' => 'Health Studies',
            'HIST' => 'History',
            'HNSC' => 'Human Nutritional Sciences',
            'HRIR' => 'Human Res. Mgmt/Indus Relat.',
            'IDM' => 'Interdisciplinary Management',
            'IMED' => 'Interdisciplinary Medicine',
            'IDES' => 'Interior Design',
            'INTB' => 'International Business',
            'KPER' => 'Kinesio, Phys Ed, & Recreation',
            'KIN' => 'Kinesiology',
            'LABR' => 'Labour Studies',
            'LARC' => 'Landscape Architecture',
            'LING' => 'Linguistics',
            'MGMT' => 'Management (Extended Ed.)',
            'MIS' => 'Management Info. Systems',
            'MSCI' => 'Management Science',
            'MKT' => 'Marketing',
            'MATH' => 'Mathematics',
            'MECG' => 'Mech. Engineering Graduate',
            'MMIC' => 'Medical Microbiology',
            'REHB' => 'Medical Rehabilitation',
            'MBIO' => 'Microbiology',
            'NATV' => 'Native Studies',
            'NURS' => 'Nursing',
            'OT' => 'Occupational Therapy',
            'OPM' => 'Operations Management',
            'PATH' => 'Pathology',
            'PHAC' => 'Pharmacology',
            'PHRM' => 'Pharmacy',
            'PHIL' => 'Philosophy',
            'PT' => 'Physical Therapy',
            'PAEP' => 'Physician Assistant Education',
            'PHYS' => 'Physics',
            'POL' => 'Polish',
            'POLS' => 'Political Studies',
            'PSYC' => 'Psychology',
            'REC' => 'Recreation Studies',
            'RLGN' => 'Religion',
            'SWRK' => 'Social Work',
            'SOC' => 'Sociology',
            'STAT' => 'Statistics',
            'SCM' => 'Supply Chain Management',
            'TRAD' => 'Traduction (St. Boniface)',
            'UKRN' => 'Ukrainian',
            'WOMN' => 'Women\'s and Gender Studies'
        );

        $terms = array(
            '202050',
            '202010',
            '201990',
            '201950',
            '201910',
            '201890',
            '201850',
            '201810',
            '201790'
        );

        $vm->courses = $courses;
        $vm->terms = $terms;
    } else {
        $tmp_path = $_SESSION['session_file'];
        $curl = new CURL($config['general']['main_url'], $tmp_path);

        $params = $_POST;
        if (!isset($params['courses']) || !isset($params['term'])) {
            die ('400 bad request');
        }
        
        $vm->term = $params['term'];

        $data = array(
            'rsts' => 'dummy',
            'crn' => 'dummy',
            'term_in' => $params['term'],
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

        $subj = array('dummy');
        foreach ($params['courses'] as $course) {
            if ($course != null && $course != '' && strlen($course) <= 4) {
                array_push($subj, $course);
            }
        }

        $data['sel_subj'] = $subj;
        $data = http_build_query($data);
        $data = preg_replace('/\%5B[0-9]\%5D/', '', $data);

        $curl->set_post($data);
        $response = $curl->get_page('/banprod/bwskfcls.P_GetCrse');

        $page = new Page($response);
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

                    $build_crses[$course_code] = $build_string;

                    $build_string = '';
                }

                $td = $course_info->item($k);
                $build_string .= trim($td->textContent) . ' ';
            }

            $subj_name = trim($subj_info->item(1)->textContent);
            $display_subjs[$subj_name] = $build_crses;
        }
        
        $vm->query_data = $display_subjs;
    }

    $vm->method = $method;
    $vm->render('search.php', true);
?>