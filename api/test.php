<?php
    error_log($_SERVER['REQUEST_METHOD'], 4);
    
    if (isset($_COOKIE['token'])) {
        error_log($_COOKIE['token'], 4);
    }
    
    setcookie('token', '123456', time() + 60 * 60, '/', '', false);
    header('Content-Type: text/json');
    http_response_code(200);
    echo(json_encode(array('token' => -1)));
?>