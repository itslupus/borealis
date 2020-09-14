<?php
    /* ===========================================================
    ||  [Authenticates the client with the banner software and returns first term]
    ||  PHP     7.2.24
    || 
    ||  POST    /Authenticate.php
    ||
    || === PARAMETERS ============================================
    ||  id
    ||  - the 7 digit student number without any leading zeros
    ||  - eg.
    ||      term = "1234567"
    ||
    ||  password
    ||  - the password
    ||  - eg.
    ||      password = "hunter2"
    ||
    || === RETURNS ===============================================
    ||  Example return data:
    ||
    ||  {
    ||      result: {
    ||          status: 0,
    ||          first_term: 201790,
    ||          last_term: 202010
    ||      }
    ||  }
    || ======================================================== */

    require_once(__DIR__ . '/include/MrManager.php');

    require_once(__DIR__ . '/persistence/MySQL.php');

    require_once(__DIR__ . '/object/CURL.php');
    require_once(__DIR__ . '/object/Token.php');
    require_once(__DIR__ . '/object/User.php');
    require_once(__DIR__ . '/object/Page.php');
    
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        // 405 method not allowed
        http_response_code(405);
        die();
    }

    if (!isset($_POST['id']) || !isset($_POST['password'])) {
        // 400 bad request
        http_response_code(400);
        die();
    }

    $manager = new MrManager();
    $config = $manager->get_config();

    //FIXME: this doesnt work, also 400 might be better suited vs 403
    // if (!is_numeric($_POST['id']) || !strlen($_POST['id']) === 7) {
    //     // 403 forbidden
    //     http_response_code(403);
    //     die();
    // }

    // create a new temporary file for cookies
    $tmp_file_path = $manager->generate_tmp_file();
    $curl = new CURL($config['main_url'], $tmp_file_path, $config['user_agent']);

    // initialize aurora
    $curl->get_page('/banprod/twbkwbis.P_WWWLogin');

    // set parameters
    $post_params = array('sid' => $_POST['id'], 'PIN' => $_POST['password']);
    $curl->set_post($post_params);

    // login with said parameters
    $response = $curl->get_page('/banprod/twbkwbis.P_ValLogin');
    $response_size = $curl->get_downloaded_size();
    
    // reset cURL instance (not sure why, i didnt comment it)
    $curl = null;

    $new_token = null;
    $sql = null;
    try {
        // initialize database now that we have sucessful login
        $sql = $manager->generate_sql_connection();

        if ($response_size < 500) {
            // get the page that has term by term account balances
            // this page will first ask us to select term, use that info to discover first/latest academic term
            $curl = new CURL($config['main_url'], $tmp_file_path, $config['user_agent']);
            $response2 = $curl->get_page('/banprod/bwskogrd.P_ViewTermGrde');

            $page = new Page($response2);

            // get only the value attribute of the option elements
            $options = $page->query('//option/@value');

            // find terms, will be something like "201790"
            $first_term = $options->item($options->length - 1)->value;
            $last_term = $options->item(0)->value;
            
            // select user from database based on student number
            $user = $sql->get_user($_POST['id']);
            if ($user === false) {
                $sql->insert_new_user($_POST['id'], $first_term, $last_term);
            } else {
                $sql->update_user($_POST['id'], $last_term);
            }

            $tmp_file_name = explode('/', $tmp_file_path);
            $tmp_file_name = end($tmp_file_name);

            $new_token = new Token();
            $new_token->generate_token();
            $new_token->set_tmp_file_name($tmp_file_name);
            $new_token->set_expires(time() + (60 * 20)); // 20 minute timeout

            $token = $sql->get_token_by_id($_POST['id']);
            if ($token !== false) {
                unlink($config['tmp_directory'] . $token->get_tmp_file_name());
                $sql->delete_token($_POST['id']);
            }

            $sql->insert_new_token($_POST['id'], $new_token);

            $manager->set_token_cookie($new_token);

            http_response_code(200);
            die(json_encode(array('result' => array(array('status' => 0, 'first_term' => $first_term, 'last_term' => $last_term)))));
        } else {
            // destroy the object, it holds a lock to our cookie file
            $curl = null;

            unlink($config['tmp_directory'] . $tmp_file_name);

            // 200 but json error?
            http_response_code(200);
            die(json_encode(array('result' => array(array('status' => 1)))));
        }
    } catch (PDOException $e) {
        // 500 internal server error
        http_response_code(500);
        die();
    }
?>