<?php
    require_once('object/CURL.php');
    require_once('object/Page.php');

    // fetch course information for winter 2020 term
    //        AAAABB
    // year --^^^^
    //            ^^-- term (90 => fall, 10 => winter, 50 => spring/summer)
    function fetch_week($config, $term) {
        // get the session file and create curl instance
        $tmp_path = $_SESSION['session_file'];
        $curl = new CURL($config['general']['main_url'], $tmp_path);

        $post_params = array('term_in' => $term);
        $curl->set_post($post_params);
        $response = $curl->get_page('/banprod/bwskfshd.P_CrseSchdDetl');

        // get the datadisplaytable
        $crse_page = new Page($response);
        $tables = $crse_page->query('//table[@class = "datadisplaytable"]');

        // these are pairs of tables
        // first one contains course info, second one contains meet times
        $courses = array();
        for ($i = 0; $i < $tables->length; $i += 2) {
            $details = array();

            $course_info = $tables->item($i);
            $course_times = $tables->item($i + 1);

            // get course name from first table
            $course_name = $crse_page->query('.//caption', $course_info);

            // get instructor info from first table children
            $course_info_detail = $crse_page->query('.//td', $course_info);
            $course_instr_name = trim($course_info_detail->item(3)->textContent);

            array_push($details, $course_instr_name);
            
            // find the meet times (its a single row) from second table
            $meet_time_els = $crse_page->query('.//td[position() < last()]', $course_times);
            foreach ($meet_time_els as $el) {
                array_push($details, $el->textContent);
            }

            $courses[$course_name->item(0)->textContent] = $details;
        }

        return $courses;
    }
?>