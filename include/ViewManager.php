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

                // include the <head>
                if ($include_extra && file_exists($this->view_path . '/head.php')) {
                    require_once($this->view_path . '/head.php');
                }

                // start body layout
                echo('<body>');
                // include the navigation
                require_once($this->view_path . '/navigation.php');

                // include the main content
                echo('<main>');
                require_once($this->view_path . '/' . $file);
                echo('</main>');

                // include the <footer>
                if ($include_extra && file_exists($this->view_path . '/footer.php')) {
                    require_once($this->view_path . '/footer.php');
                }
                
                echo('</body>');
                echo('</html>');
            }

            echo(ob_get_clean());
        }
    }
?>