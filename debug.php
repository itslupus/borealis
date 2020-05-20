<?php
    session_start();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $_SESSION['term'] = $_POST['term'];
    }

    var_dump($_SESSION);
    echo('<form action = debug.php method = POST><input type = text name = term></input><input type = submit value = go></input></form>');
    require_once(__DIR__ . '/view/alpha/navigation.php');
?>