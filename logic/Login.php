<?php
    require_once('../include/Initializer.php');
    require_once('../persistence/MySQL.php');

    require_once('../object/CURL.php');
    require_once('../object/Page.php');
    require_once('../object/Token.php');

    $config;

    try {
        $init = new Initializer();

        $config = $init->get_config();
        $init->verify_session();

        header('Location: /home.php');
        die();
    } catch (InitializerConfigInvalid $e) {
        die('invalid config file');
    } catch (InitializerInvalidSession $e) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $sql = new MySQL($config);

            // read in id and password, even if they don't exist
            $stu_id = $_POST['id'];
            $password = $_POST['password'];
            
            if ($stu_id != '' && $password != '') {
                // generate the tmp file for cookies, this stays with the client
                $tmp_folder = $config['general']['tmp_directory'];
                $tmp_prefix = $config['general']['tmp_prefix'];
                $tmp_file_path = tempnam('../' . $tmp_folder, $tmp_prefix . '-');

                // read the main banner url and create curl instance
                $main_url = $config['general']['main_url'];
                $curl = new CURL($main_url, $tmp_file_path);
                
                // init the page and initial session cookie from aurora
                $curl->get_page('/banprod/twbkwbis.P_WWWLogin');
                
                // login
                $post_params = array('sid' => $stu_id, 'PIN' => $password);
                $curl->set_post($post_params);
                $response = $curl->get_page('/banprod/twbkwbis.P_ValLogin');

                // response size of < 500 bytes indicates login success
                $response_size = $curl->get_downloaded_size();
                if ($response_size < 500) {
                    // register new user and token if not exists
                    try {
                        $user = $sql->get_user($stu_id);
                        if ($user === false) {
                            error_log('create new user', 4);
                            $sql->insert_new_user($stu_id);
                        } else {
                            error_log('update user', 4);
                            $sql->update_user_last_login($stu_id, time());
                        }

                        $token = $sql->get_token($stu_id);
                        if ($token === false) {
                            error_log('create new token', 4);
                            $token = new Token();
                            $token->generate_token();
                            $token->set_tmp_file_path($tmp_file_path);
                            $token->set_expires(time());

                            $sql->insert_new_token($stu_id, $token);
                        } else {
                            error_log('token exists', 4);
                            $sql->update_token_timeout($stu_id, time());
                        }

                    } catch (PDOException $e) {
                        die('500 database error');
                    }

                    $_SESSION['session_file'] = $tmp_file_path;

                    // replace the + from the url encoded form
                    $parsed_response = str_replace('+', ' ', $response);

                    // find the name of the client and save it since we have it on hand
                    $match;
                    preg_match("/(?<=Welcome, ).*(?=, to)/", $parsed_response, $match);
                    $_SESSION['name'] = $match[0];


                    // determine the current term and remember it
                    $term = date('Y');
                    $month = date('n');
                    if ($month >= 9) {
                        $month = 90;
                    } else if ($month >= 5) {
                        $month = 50;
                    } else {
                        $month = 10;
                    }
                    $_SESSION['term'] = $term . strval($month);

                    header('Location: /home.php');
                    die();
                } else {
                    // invalid login
                    header('Location: Logout.php');
                    die();
                }
            } else {
                // invalid parameters
            }
        } else {
            // invalid method
        }
    }

    header('Location: /');
    die();
?>