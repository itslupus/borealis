<?php
    require_once('../include/Initializer.php');
    require_once('../object/CURL.php');
    require_once('../object/Page.php');

    $config;

    try {
        $init = new Initializer();

        $config = $init->read_config();
        $init->verify_session();

        header('Location: /home.php');
        die();
    } catch (InitializerConfigInvalid $e) {
        die('invalid config file');
    } catch (InitializerInvalidSession $e) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // read in id and password, even if they don't exist
            $stu_id = $_POST['id'];
            $password = $_POST['password'];

            if (isset($stu_id) && isset($password)) {
                // verify student id length and type
                if (strlen($stu_id) === 7 && is_numeric($stu_id)) {
                    // read the main banner url and create curl instance
                    // if we can log on fine, this curl instance will be saved in $_SESSION
                    $main_url = $config['general']['main_url'];
                    $curl = new CURL($main_url);
                    
                    // init the page and initial session cookie from aurora
                    $curl->get_page('/banprod/twbkwbis.P_WWWLogin');
                    
                    // login
                    $post_params = array('sid' => $stu_id, 'PIN' => $password);
                    $curl->set_post($post_params);
                    $response = $curl->get_page('/banprod/twbkwbis.P_WWWLogin');

                    $response_size = $curl->get_downloaded_size();
                    if ($response_size < 500) {
                        $_SESSION['curl'] = $curl;
                        $_SESSION['pages']['P_MainMnu'] = new Page($response);

                        header('Location: /home.php');
                        die();
                    } else {
                        // invalid id/password
                    }
                } else {
                    // invalid student id format
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