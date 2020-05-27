<style>
    .week-container {
        display: flex;
        flex-direction: row;
    }

    .week-container > .day {
        flex: 1 0 auto;
    }

    .week-container > .day:nth-last-of-type(odd) {
        background-color: palegreen;
    }

    .week-container > .day > .course:nth-last-of-type(odd) {
        background-color: gainsboro;
    }
</style>

<p>week view</p>
<div class = 'dropdown'>
    <div class = 'dropdown-header padded'>week classes</div>
    <div class = 'dropdown-content'>
        <div class = 'week-container'>
            <?php foreach ($week as $day => $courses) { ?>
                <div class = 'day'>
                    <b><p style = 'background-color: salmon;'><?=$day?></p></b>
                    <?php foreach ($courses as $course) { ?>
                        <div class = 'course'>
                            <p>[<?=$course->get_section()?>] <?=$course->get_subj()?> <?=$course->get_level()?></p>
                            <p><?=$course->get_instructor()?></p>
                            <p><?=$course->get_meet_times()[0]->get_time_low()?> to <?=$course->get_meet_times()[0]->get_time_high()?></p>
                            <p><?=$course->get_meet_times()[0]->get_location()?></p>
                        </div>
                    <?php } ?>
                </div>
            <?php } ?>
        </div>
    </div>
</div>