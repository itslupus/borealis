<?php
    /* ===========================================================
    ||  [Fetches the last academic term's tuition totals]
    ||  PHP     7.2.24
    || 
    ||  POST    /FetchAccSummary.php
    ||
    || === NOTES =================================================
    ||  The payment field in items[] is current unused
    ||
    || === RETURNS ===============================================
    ||  Example return data:
    ||
    ||  {
    ||      result: {
    ||          balance: "$1234.00",
    ||          items: [
    ||              {
    ||                  desc: "Fac of Science Tuition",
    ||                  charge: "$500.00",
    ||                  payment: ""
    ||              }
    ||          ]
    ||      }
    ||  }
    || ======================================================== */

    require_once(__DIR__ . '/include/MrManager.php');
    require_once(__DIR__ . '/object/Page.php');

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

    $manager = new MrManager($_COOKIE['token']);
    
    $page = $manager->get_page('/banprod/bwskoacc.P_ViewAcct_disp');
    
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