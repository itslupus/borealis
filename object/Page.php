<?php
    class Page {
        private $html;
        private $dom_obj;
        private $xpath_obj;

        public function __construct($html) {
            libxml_use_internal_errors(true);

            $this->html = $html;

            $this->dom_obj = new DOMDocument();
            $this->dom_obj->loadHTML($this->html);

            $this->xpath_obj = new DOMXPath($this->dom_obj);
        }

        public function query($query, $relative = null) {
            $elements;

            if ($relative !== null) {
                $elements = $this->xpath_obj->query($query, $relative);
            } else {
                $elements = $this->xpath_obj->query($query);
            }

            return $elements;
        }
        
        public function get_html() {
            return $this->html;
        }
    }
?>