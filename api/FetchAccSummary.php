<?php
    /* ===========================================================
    ||  [Fetches the last academic term's tuition totals]
    ||  PHP     7.2.24
    || 
    ||  POST    /api/FetchAccSummary.php
    ||
    ||  RETURN  {
    ||              result: {
    ||                  balance: $1234.00
    ||                  items: [
    ||                      {
    ||                          desc: 'Fac of Science Tuition'
    ||                          charge: $1234.00
    ||                          payment: <!>
    ||                      },
    ||                      ......
    ||                  ]
    ||              }
    ||          }
    || ======================================================== */

    require_once($_SERVER['DOCUMENT_ROOT'] . '/api/include/MrManager.php');

    require_once($_SERVER['DOCUMENT_ROOT'] . '/api/object/CURL.php');
    require_once($_SERVER['DOCUMENT_ROOT'] . '/api/object/Token.php');
    require_once($_SERVER['DOCUMENT_ROOT'] . '/api/object/Page.php');

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        // 405 method not allowed
        http_response_code(405);
        die();
    }

    if (!isset($_COOKIE['token'])) {
        // 400 bad request
        http_response_code(400);
        die();
    }

    $manager = new MrManager();
    $config = $manager->get_config();
    $token = null;

    try {
        $token = $manager->validate_token($_COOKIE['token']);
        $token = $manager->regenerate_token($token);

        $manager->validate_banner_session($token);
        $manager->set_token_cookie($token);
    } catch (MrManagerInvalidToken $e) {
        // 401 unauth
        http_response_code(401);
        die('invalid token');
    } catch (MrManagerInvalidBannerSession $e2) {
        // 401 unauth
        http_response_code(401);
        die('invalid banner');
    } catch (MrManagerExpiredToken $e3) {
        // 401 unauth
        http_response_code(401);
        die('expired token');
    }
    
    $curl = $manager->get_curl_object($token->get_tmp_file_name());
    $response = $curl->get_page('/banprod/bwskoacc.P_ViewAcct_disp');

    $page = new Page($response);
    $table_rows = $page->query('//table[@class = "datadisplaytable"]/tr');

    $result = array(
        'result' => array(
            'balance' => '',
            'items' => array()
        )
    );

    $total_balance = trim($page->query('.//td/p', $table_rows->item(0))->item(0)->textContent);
    $result['result']['balance'] = $total_balance;

    for ($i = 4; $i < $table_rows->count(); $i++) {
        $row = $table_rows->item($i);
        $row_els = $page->query('.//td', $row);

        if ($row_els->count() === 1) break;

        $desc = trim($row_els->item(0)->textContent);
        $charge = trim($page->query('.//p', $row_els->item(1))->item(0)->textContent);
        $payment = trim($page->query('.//p', $row_els->item(2))->item(0)->textContent);

        $item = array(
            'desc' => $desc,
            'charge' => $charge,
            'payment' => $payment
        );

        array_push($result['result']['items'], $item);
    }

    header('Content-Type: text/json');
    http_response_code(200);
    echo(json_encode($result));
?>