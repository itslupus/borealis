<?php
    require_once('object/CURL.php');
    require_once('object/Page.php');
    require_once('object/AccSummary.php');
    require_once('object/AccSummaryDetail.php');

    function fetch_acc_summary($config) {
        $tmp_path = $_SESSION['session_file'];
        $curl = new CURL($config['general']['main_url'], $tmp_path);

        $response = $curl->get_page('/banprod/bwskoacc.P_ViewAcct_disp');

        $page = new Page($response);
        $table_rows = $page->query('//table[@class = "datadisplaytable"]/tr');

        $total_balance = trim($page->query('.//td/p', $table_rows->item(0))->item(0)->textContent);

        $acc_summary = new AccSummary();
        $acc_summary->set_total_amnt($total_balance);

        for ($i = 4; $i < $table_rows->count(); $i++) {
            $row = $table_rows->item($i);
            $row_els = $page->query('.//td', $row);

            if ($row_els->count() === 1) break;

            $desc = trim($row_els->item(0)->textContent);
            $charge = trim($page->query('.//p', $row_els->item(1))->item(0)->textContent);
            $payment = trim($page->query('.//p', $row_els->item(2))->item(0)->textContent);

            $detail = new AccSummaryDetail($desc, $charge, $payment);
            $acc_summary->add_detail($detail);
        }
        return $acc_summary;
    }
?>