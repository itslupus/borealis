<?php
    class AccSummaryDetail {
        private $cost_name;
        private $cost_amnt;
        private $cost_amnt_paid;

        public function __construct($name, $amnt, $paid) {
            $this->cost_name = $name;
            $this->cost_amnt = ($amnt == '') ? 0 : $amnt;
            $this->cost_amnt_paid = ($paid == '') ? 0 : $paid;
        }

        public function get_name() {
            return $this->cost_name;
        }

        public function get_amnt() {
            return $this->cost_amnt;
        }

        public function get_amnt_paid() {
            return $this->cost_amnt_paid;
        }
    }
?>