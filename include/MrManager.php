<?php
    class MrManager {
        private $config_data = null;

        public function get_config() {
            if ($this->config_data === null) {
                $this->config_data = parse_ini_file(__DIR__ . '/../_config.ini', true);

                $verify_keys = ['general', 'sql'];
                foreach ($verify_keys as $key) {
                    if (!array_key_exists($key, $this->config_data)) {
                        throw new MrManagerInvalidConfig();
                    }
                }
            }
            
            return $this->config_data;
        }

        public function generate_tmp_file() {
            $this->get_config();

            $tmp_folder = $this->config_data['general']['tmp_directory'];
            $tmp_prefix = $this->config_data['general']['tmp_prefix'];
            $tmp_file_path = tempnam('../' . $tmp_folder, $tmp_prefix . '-');

            return $tmp_file_path;
        }
    }

    class MrManagerInvalidConfig extends Exception {}
?>