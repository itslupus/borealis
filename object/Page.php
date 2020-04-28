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

        public function get_elements_by_tag($tag, $relative = null) {
            $elements;

            if ($relative !== null) {
                $elements = $this->xpath_obj->query('.//' . $tag, $relative);
            } else {
                $elements = $this->xpath_obj->query('//' . $tag);
            }

            return $elements;
        }

        public function get_elements_by_class($tag, $class, $relative = null) {
            $elements;

            if ($relative !== null) {
                $elements = $this->xpath_obj->query('.//' . $tag . '[@class="' . $class . '"]', $relative);
            } else {
                $elements = $this->xpath_obj->query('//' . $tag . '[@class="' . $class . '"]');
            }

            return $elements;
        }

        public function get_elements_by_attr_val($tag, $attr, $val, $relative = null) {
            $elements;

            if ($relative !== null) {
                $elements = $this->xpath_obj->query('.//' . $tag . '[contains(@' . $attr . ', \'' . $val . '\')]', $relative);
            } else {
                $elements = $this->xpath_obj->query('//' . $tag . '[contains(@' . $attr . ', \'' . $val . '\')]');
            }

            return $elements;
        }

        public function get_last_elements($tag, $relative = null) {
            $last_els;

            if ($relative !== null) {
                $last_els = $this->xpath_obj->query('.//' . $tag . '[position() < last()]', $relative);
            } else {
                $last_els = $this->xpath_obj->query('//' . $tag . '[position() < last()]');
            }

            return $last_els;
        }

        public function get_html() {
            return $this->html;
        }
    }
?>