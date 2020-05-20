<?php
    class Course {
        private $name;
        private $subj;
        private $level;
        private $section;
        private $instructor;

        private $meet_times = array();

        public function set_name(string $name) {
            $this->name = $name;
        }

        public function set_subj(string $subj) {
            $this->subj = $subj;
        }

        // note: i think some engineering course out there has letters in the level
        public function set_level(string $level) {
            $this->level = $level;
        }

        public function set_section(string $section) {
            $this->section = $section;
        }

        public function set_instructor(string $instructor) {
            $this->instructor = $instructor;
        }

        public function add_meet_time(CourseMeetTime $time) {
            array_push($this->meet_times, $time);
        }

        public function get_name() {
            return $this->name;
        }

        public function get_subj() {
            return $this->subj;
        }

        public function get_level() {
            return $this->level;
        }

        public function get_section() {
            return $this->section;
        }

        public function get_instructor() {
            return $this->instructor;
        }

        public function get_meet_times() {
            return $this->meet_times;
        }
    }
?>