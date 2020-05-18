<?php
    class CURL {
        private $main_url;
        private $tmp_file_path;
        private $curl;

        public function __construct($main_url, $tmp_file_path) {
            $this->main_url = $main_url;
            $this->tmp_file_path = $tmp_file_path;

            $this->curl = curl_init();
            
            curl_setopt($this->curl, CURLOPT_COOKIEJAR, $this->tmp_file_path);
            curl_setopt($this->curl, CURLOPT_COOKIEFILE, $this->tmp_file_path);
            curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($this->curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/42.0.2311.135 Safari/537.36 Edge/12.246');
        }

        public function set_post($data) {
            curl_setopt($this->curl, CURLOPT_POSTFIELDS, $data);
        }

        public function get_page($url) {
            curl_setopt($this->curl, CURLOPT_URL, $this->main_url . $url);
            $result = curl_exec($this->curl);

            error_log('DOWNLOADED ' . number_format($this->get_downloaded_size() / 1024.0, 2) . " KB\t" . $url, 4);

            return $result;
        }

        public function get_downloaded_size() {
            return curl_getinfo($this->curl, CURLINFO_CONTENT_LENGTH_DOWNLOAD);
        }
    }
?>