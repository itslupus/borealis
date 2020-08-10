<?php
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

    $post_params = array('term_in' => $_POST['term']);
    $page = $manager->get_page('/banprod/bwskfshd.P_CrseSchdDetl', $post_params);
        
    // get the datadisplaytable
    $crse_page = new Page($response);
    $tables = $crse_page->query('//table[@class = "datadisplaytable"]');

        // return array of courses
        $courses = array();
        for ($i = 0; $i < $tables->length; $i += 2) {
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
            
                $raw_times = trim($meet_time_els->item($j + 1)->textContent);
                if ($raw_times === 'TBA') {
                    $meet_time_new->set_time_low(0);
                    $meet_time_new->set_time_high(0);
                } else {
                    $times = explode(' - ', $raw_times);
                    $meet_time_new->set_time_low($times[0]);
                    $meet_time_new->set_time_high($times[1]);
                }

                $dates = explode(' - ', trim($meet_time_els->item($j + 4)->textContent));
                $meet_time_new->set_date_low(strtotime($dates[0]));
                $meet_time_new->set_date_high(strtotime($dates[1]));

                $course_new->add_meet_time($meet_time_new);
            }

            array_push($courses, $course_new);
        }

        return $courses;
?>