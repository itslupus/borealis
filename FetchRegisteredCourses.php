<?php
    /* ===========================================================
    ||  [Fetches registered courses based on term]
    ||  PHP     7.2.24
    || 
    ||  POST    /FetchRegisteredCourses.php
    ||
    || === NOTES =================================================
    || Both "waitlisted" and "registered" groups contain the same information.
    || The only difference is that waitlisted has an extra key denoting waitlist position.
    ||
    || === PARAMETERS ============================================
    ||  term
    ||  - the term to query for registered courses
    ||  - eg.
    ||      term = "202090"
    ||
    || === RETURNS ===============================================
    ||  Example return data:
    ||
    ||  {
    ||      result: {
    ||          waitlisted: {
    ||              "Introduction to Calculus - MATH 1500 - A01": {
    ||                  details: {
    ||                      crn: 12345,
    ||                      instr: "Person Name",
    ||                      credit: "3.000",
    ||                      wait_pos: "5"
    ||                  },
    ||                  meets: {
    ||                      {
    ||                          type: "On-Line Study",
    ||                          time: "TBA",
    ||                          days: "",
    ||                          location: "TBA",
    ||                          length: "May 07, 2018 - Aug 03, 2018"
    ||                      }
    ||                  }
    ||              },
    ||          },
    ||          registered: {
    ||              "Introduction to Calculus - MATH 1500 - B02" : {
    ||                  details: {
    ||                      crn: 12345,
    ||                      instr: "Person Name",
    ||                      credit: "3.000"
    ||                  },
    ||                  meets: {
    ||                      {
    ||                          type: "Tutorial",
    ||                          time: "9:40 am - 10:29 am",
    ||                          days: "MWF",
    ||                          location: "TIER 403",
    ||                          length: "May 07, 2018 - Aug 03, 2018"
    ||                      }
    ||                  }
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

    if (!isset($_COOKIE['token']) || !isset($_POST['term'])) {
        // 400 bad request
        http_response_code(400);
        die();
    }

    $manager = new MrManager($_COOKIE['token']);
    
    $page = $manager->get_page('/banprod/bwskogrd.P_ViewGrde', array('term_in' => $_POST['term']));
    $tables = $page->query('//div[@class = "pagebodydiv"]/table[@class = "datadisplaytable"]');

    $return_result = array(
        'waitlisted' => array(),
        'confirmed' => array()
    );

    for ($i = 0; $i < $tables->length; $i += 2) {
        $new_course = array(
            'details' => array(),
            'meets' => array()
        );

        // - waitlist details omitted if no waitlist (length of 8 vs 10)
        // - if registered, "status" will state registered vs waitlist
        // 0 Associated Term:       Winter 2021
        // 1 CRN:                   50181
        // 2 Status: 	            Waitlist on Jul 29, 2020
        // 3 Waitlist Position:     16
        // 4 Notification Expires:  <blank>
        // 5 Assigned Instructor:   Shaowei Wang
        // 6 Grade Mode: 	        Standard Letter Grade
        // 7 Credits:               0.000
        // 9 Level:                 Undergraduate
        // 10 Campus:               Main (Fort Garry & Bannatyne)

        $table_info = $tables->item($i);
        $course_info = $page->query('.//td', $table_info);

        $is_waitlist = $course_info->length === 10;
        
        $new_course['details']['crn'] = trim($course_info->item(1)->textContent);
        $new_course['details']['instr'] = trim($course_info->item($is_waitlist ? 5 : 3)->textContent);
        $new_course['details']['credit'] = trim($course_info->item($is_waitlist ? 7 : 5)->textContent);

        if ($is_waitlist) {
            $new_course['details']['wait_pos'] = trim($course_info->item(3)->textContent);
        }

        // - sample multi line times
        // - has 7 columns
        //      0                    1                2         3                   4                      5                 7
        // 0 Lecture        10:45 am - 11:45 am     MTWRF   TIER 206    May 07, 2018 - May 30, 2018     Science 	Robert D. Borgersen (P)
        // 1 Lecture        10:45 am - 11:45 am     MTWRF   TIER 206    Jun 04, 2018 - Jun 26, 2018     Science 	Robert D. Borgersen (P)
        // 2 Final Exam     9:00 am - 11:00 am      F       room        Jun 29, 2018 - Jun 29, 2018 	Science 	Robert D. Borgersen (P)
        
        $table_times = $tables->item($i + 1);
        $course_time_rows = $page->query('.//tr[position() > 1]', $table_times);
        
        foreach ($course_time_rows as $row) {
            $columns = $page->query('.//td', $row);

            array_push($new_course['meets'], array(
                'type' => trim($columns->item(0)->textContent),
                'time' => trim($columns->item(1)->textContent),
                'days' => trim($columns->item(2)->textContent),
                'location' => trim($columns->item(3)->textContent),
                'length' => trim($columns->item(4)->textContent)
            ));
        }

        $course_name = $page->query('.//caption', $table_info)->item(0)->textContent;
        $course_name = trim($course_name);
        
        $return_result[$is_waitlist ? 'waitlisted' : 'confirmed'][$course_name] = $new_course;
    }

    header('Content-Type: text/json');
    http_response_code(200);
    echo(json_encode(array('result' => $return_result)));
?>