<p>week view</p>
<div class = 'dropdown'>
    <div class = 'dropdown-header padded'>week classes</div>
    <div class = 'dropdown-content'>
        <?php if(count($courses) == 0) { ?>
            <p>no courses this term</p>
        <?php } ?>
        <?php foreach($courses as $k => $v){ ?>
            <p><?=$k?></p>
            <?php foreach($v as $z){ ?>
                <p>> <?=$z?></p>
            <?php } ?>
            <br>
        <?php } ?>
    </div>
</div>