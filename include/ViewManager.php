<?php
    require_once('Initializer.php');

    class ViewManager {
        private $view_path;
        private $output = array();

        /*********************************
         * Constructor
         * 
         * @throws  InitializerConfigInvalid
         */
        public function __construct() {
            $init = new Initializer();

            $config = $init->read_config();
            $this->view_path = $config['general']['current_view'];
        }

        /*********************************
         * Magic setter
         * 
         * @param any $k the key to index
         * @param any $v the value to set
         */
        public function __set($k, $v) {
            $this->output[$k] = $v;
        }

        /*********************************
         * Magic getter
         * 
         * @param any $k the key to get
         */
        public function __get($k) {
            return $this->output[$k];
        }

        /*********************************
         * Renders the given file with output
         * 
         * @param any $file the body file to render
         * @param any $include_extra set to true to also include <head> and <footer>
         */
        public function render($file, $include_extra) {
            ob_start();

            if (file_exists($this->view_path . '/' . $file)) {
                extract($this->output);

                echo('<html>');

                if ($include_extra && file_exists($this->view_path . '/head.php')) {
                    require_once($this->view_path . '/head.php');
                }

                echo('<body>');
                require_once($this->view_path . '/' . $file);
                echo('</body>');

                if ($include_extra && file_exists($this->view_path . '/footer.php')) {
                    require_once($this->view_path . '/footer.php');
                }

                echo('</html>');
            }

            echo(ob_get_clean());
        }
    }
?>