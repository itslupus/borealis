<?php
    // method not allowed
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        die();
    }

    // bad request
    if (!isset($_POST['name'])) {
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

    $query = strtolower($_POST['name']);

    $result = [];
    if ($query !== '') {
        foreach ($courses as $k => $v) {
            if (count($result) > 5)
                break;
    
            $key = strtolower($k);
            $value = strtolower($v);
            if ((strpos($key, $query) !== false || strpos($value, $query) !== false)) {
                array_push($result, [$k => $v]);
            }
        }
    }

    http_response_code(200);
    echo(json_encode($result));
?>