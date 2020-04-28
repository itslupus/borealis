<a href = 'logic/Logout.php'>logic/Logout.php</a>
<a href = 'home.php'>home.php</a>
<a href = 'search.php'>search.php</a>
<p>welcome back <?=$name?></p><br>
<?php foreach($courses as $k => $v){ ?>
    <p><?=$k?></p>
    <?php foreach($v as $z){ ?>
        <p>> <?=$z?></p>
    <?php } ?>
    <br>
<?php } ?>