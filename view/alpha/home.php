<p>welcome back <?=$name?></p><br>
<div class = 'dropdown'>
    <div class = 'dropdown-header padded'>classes</div>
    <div class = 'dropdown-content'>
        <?php if(count($courses) == 0) { ?>
            <p>no courses this term</p>
        <?php } ?>
        <?php foreach($courses as $course){ ?>
            <p>[<?=$course->get_subj()?> <?=$course->get_level()?>] [<?=$course->get_section()?>] <?=$course->get_name()?></p>
            <p><?=$course->get_instructor()?></p>
            <br>
        <?php } ?>
    </div>
</div>
<br>
<div class = 'dropdown'>
    <div class = 'dropdown-header padded'>fees</div>
    <div class = 'dropdown-content'>
        <p>summary: <?=$summary_total?></p>
        <table border = 1>
            <?php foreach($summary_details as $detail){ ?>
                <tr>
                    <td><?=$detail->get_name()?></td>
                    <td><?=$detail->get_amnt()?></td>
                    <td>-<?=$detail->get_amnt_paid()?></td>
                </tr>
            <?php } ?>
        </table>
    </div>
</div>