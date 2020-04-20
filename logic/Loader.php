<?php
    class Loader {
        private $ini_data;

        public function read_init() {
            $this->ini_data = parse_ini_file('_config.ini.php', true);

            $verify_keys = ['general', 'sql'];
            foreach ($verify_keys as $key) {
                if (!array_key_exists($key, $this->ini_data)) {
                    throw new LoaderMissingIniSection();
                }
            }
        }

        public function init_session() {
            session_start();
        }
    }

    class LoaderMissingIniSection extends Exception {}
?>