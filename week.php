<?php
    require_once('include/Initializer.php');
    require_once('include/ViewManager.php');

    require_once('logic/FetchWeek.php');
    require_once('logic/FetchAccSummary.php');

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

    $courses = fetch_week($config, $_SESSION['term']);

    //TODO: remember to add sat and sun
    $week = array(
        'Monday' => array(),
        'Tuesday' => array(),
        'Wednesday' => array(),
        'Thursday' => array(),
        'Friday' => array()
    );
    
    foreach ($courses as $course) {
        $meet = $course->get_meet_times()[0]->get_days();

        if ($meet === '')
            continue;

        if (strpos($meet, 'M') !== false)
            array_push($week['Monday'], $course);

        if (strpos($meet, 'T') !== false)
            array_push($week['Tuesday'], $course);

        if (strpos($meet, 'W') !== false)
            array_push($week['Wednesday'], $course);

        if (strpos($meet, 'R') !== false)
            array_push($week['Thursday'], $course);

        if (strpos($meet, 'F') !== false)
            array_push($week['Friday'], $course);
    }

    foreach ($week as $day) {
        usort($day, function($course1, $course2) {
            $meet1 = strtotime($course1->get_meet_times()[0]->get_time_low());
            $meet2 = strtotime($course2->get_meet_times()[0]->get_time_low());

            return ($meet1 < $meet2) ? -1 : 1;
        });
    }
    
    $vm->week = $week;
    $vm->render('week.php', true);
?>