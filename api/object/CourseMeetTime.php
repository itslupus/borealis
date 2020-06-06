<?php
    class CourseMeetTime {
        private $type;
        private $days;
        private $location;

        private $time_low;
        private $time_high;
        private $date_low;
        private $date_high;

        public function set_type(string $type) {
            $this->type = $type;
        }

        public function set_days(string $days) {
            $this->days = $days;
        }

        public function set_location(string $location) {
            $this->location = $location;
        }

        public function set_time_low(string $time_low) {
            $this->time_low = $time_low;
        }

        public function set_time_high(string $time_high) {
            $this->time_high = $time_high;
        }

        public function set_date_low(int $date_low) {
            $this->date_low = $date_low;
        }

        public function set_date_high(int $date_high) {
            $this->date_high = $date_high;
        }

        public function get_type() {
            return $this->type;
        }

        public function get_days() {
            return $this->days;
        }

        public function get_location() {
            return $this->location;
        }

        public function get_time_low() {
            return $this->time_low;
        }

        public function get_time_high() {
            return $this->time_high;
        }

        public function get_date_low() {
            return date('Y/m/d', $this->date_low);
        }

        public function get_date_high() {
            return date('Y/m/d', $this->date_high);
        }
    }
?>