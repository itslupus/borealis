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

            $config = $init->get_config();
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

        public function render_head() {
            if (file_exists($this->view_path . '/head.php')) {
                require_once($this->view_path . '/head.php');
            }
        }

        public function render_navigation() {
            require_once($this->view_path . '/navigation.php');
        }

        public function render_footer() {
            if (file_exists($this->view_path . '/footer.php')) {
                require_once($this->view_path . '/footer.php');
            }
        }

        /*********************************
         * Renders the given file with output
         * 
         * @param any $file the body file to render
         * @param any $include_extra set to true to also include <head>, <footer> and the navigation links
         */
        public function render($file, $include_extra) {
            ob_start();

            if (file_exists($this->view_path . '/' . $file)) {
                extract($this->output);

                echo('<html>');

                $this->render_head();

                // start body layout
                echo('<body>');

                $this->render_navigation();

                // include the main content
                echo('<main>');
                require_once($this->view_path . '/' . $file);
                echo('</main>');

                echo('</body>');

                $this->render_footer();
                
                echo('</html>');
            }

            echo(ob_get_clean());
        }
    }
?>