<?php
    require_once('object/CURL.php');
    require_once('object/Page.php');
    require_once('object/Course.php');
    require_once('object/CourseMeetTime.php');

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

        // return array of courses
        $courses = array();
        for ($i = 2; $i < $tables->length; $i += 2) {
            $course_new = new Course();

            $details = array();

            $course_info = $tables->item($i);
            $course_times = $tables->item($i + 1);

            // get course name and details from first table
            $course_name_full = $crse_page->query('.//caption', $course_info)->item(0);
            $split = explode(' - ', trim($course_name_full->textContent));
            $subj_info = explode(' ', $split[1]);

            $course_new->set_name($split[0]);
            $course_new->set_subj($subj_info[0]);
            $course_new->set_level($subj_info[1]);
            $course_new->set_section($split[2]);
            
            // get instructor info from first table children
            $course_info_detail = $crse_page->query('.//td', $course_info);
            $course_instr_name = trim($course_info_detail->item(3)->textContent);

            $course_new->set_instructor($course_instr_name);
            
            $meet_time_els = $crse_page->query('.//td[position() < last()]', $course_times);
            for ($j = 0; $j < $meet_time_els->length; $j += 6) {
                $meet_time_new = new CourseMeetTime();

                $meet_time_new->set_type(trim($meet_time_els->item($j)->textContent));
                $meet_time_new->set_days(trim($meet_time_els->item($j + 2)->textContent));
                $meet_time_new->set_location(trim($meet_time_els->item($j + 3)->textContent));
            
                $times = explode(' - ', trim($meet_time_els->item($j + 1)->textContent));
                $meet_time_new->set_time_low($times[0]);
                $meet_time_new->set_time_high($times[1]);

                $dates = explode(' - ', trim($meet_time_els->item($j + 4)->textContent));
                $meet_time_new->set_date_low(strtotime($dates[0]));
                $meet_time_new->set_date_high(strtotime($dates[1]));

                $course_new->add_meet_time($meet_time_new);
            }

            array_push($courses, $course_new);
        }

        return $courses;
    }
?>