<?php
    require_once('../include/Initializer.php');
    require_once('../object/CURL.php');
    require_once('../object/Page.php');

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
            // fwrite($file, "aurora.umanitoba.ca\tFALSE\t/banprod/\tFALSE\t0\tSESSID\there\n");

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
                    // $_SESSION['term'] = $term . strval($month);
                    $_SESSION['term'] = '202010';

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