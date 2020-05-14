<?php
    echo("<a href = 'logic/Logout.php'>logic/Logout.php</a><a href = 'debug.php'>debug.php</a><a href = 'aurora.php'>aurora.php</a><a href = 'home.php'>home.php</a><a href = 'search.php'>search.php</a><br><br>");

    session_start();
    var_dump($_SESSION);
?>