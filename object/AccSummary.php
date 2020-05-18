<?php
    class AccSummary {
        private $total_amnt;
        private $details = array();

        public function set_total_amnt($amnt) {
            $this->total_amnt = ($amnt == '') ? 0 : $amnt;
        }

        public function add_detail(AccSummaryDetail $detail) {
            array_push($this->details, $detail);
        }

        public function get_total_amnt() {
            return $this->total_amnt;
        }

        public function get_details() {
            return $this->details;
        }
    }
?>