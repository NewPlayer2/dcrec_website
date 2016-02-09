<?php

class Application Extends Singleton {
    protected static $_instance = null;
    public $config;
    private $app_output;
    private $navi;
    public $navi_items = array();

    protected function __construct () {
        $this->config =& Config::get_instance();
        $this->config->load('app');
        $this->navi = Navigation::get_instance();
    }

    public function site_url ($arg = "") {
        $appurl = $this->config->get('appurl');
        $arg = trim(ltrim($arg, '/'));
        if (strpos($arg, $appurl) === 0){
            return $arg;
        }
        return rtrim($appurl, '/') . "/" . $arg;
    }

    public function base_url ($arg) {
        $baseurl = $this->config->get('baseurl');
        $arg = trim(ltrim($arg, '/'));
        if (strpos($arg, $baseurl) === 0){
            return $arg;
        }
        return rtrim($baseurl, '/') . "/" . $arg;
    }

    public function current_url ($use_forwarded_host = false) {
        $ssl = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? true : false;
        $sp = strtolower($_SERVER['SERVER_PROTOCOL']);
        $protocol = substr($sp, 0, strpos($sp, '/')) . (($ssl) ? 's' : '');
        $port = $_SERVER['SERVER_PORT'];
        $port = ((!$ssl && $port == '80') || ($ssl && $port == '443')) ? '' : ':' . $port;
        $host = ($use_forwarded_host && isset($_SERVER['HTTP_X_FORWARDED_HOST'])) ? $_SERVER['HTTP_X_FORWARDED_HOST'] : (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : null);
        $host = isset($host) ? $host : $_SERVER['SERVER_NAME'] . $port;
        // messy shit above elegantly copied from stackoverflow
        return $this->site_url($protocol . '://' . $host . $_SERVER['REQUEST_URI']);
    }

    public function redirect ($location) {
        header("Location: " . $this->site_url($location));
        exit;
    }

    public function execute () {
        try {
            $this->segments = $this->parse_route();
            $this->filter_segments($this->segments);

            if (isset($this->segments[1]) && $this->segments[1]) {
                $controller = $this->segments[1];
            } else {
                $controller = $this->config->get('default_controller');
            }

            $c_path = BASEPATH . "/controllers/" . $controller . ".php";
            if (is_readable($c_path)) {
                require_once($c_path);
            } else {
                $this->show_404();
            }

            if (isset($this->segments[2]) && strlen($this->segments[2])) {
                $method = $this->segments[2];
            } else {
                $method = "index";
            }

            if (!method_exists($controller, $method)) {
                $this->show_404();
            }

            if (!is_callable(array($controller, $method))) {
                $this->show_403();
            }

            $this->app_output = "";
            ob_start();
            $ctrl_instance = new $controller();
            $args = array_slice($this->segments, 3);
            call_user_func_array(array($ctrl_instance, $method), $args);
            $this->app_output .= ob_get_contents();
            ob_end_clean();

        } catch (Exception $e) {
            throw $e;
        }

        $this->output();
    }

    private function filter_segments (&$segments) {
        $pattern = $this->config->get('segment_filter_pattern');
        foreach ($segments as $key => &$value) {
            if(!empty($value) && !preg_match($pattern, $value)) {
                $value = "REPLACED";
                $this->show_404();
            }
        }
    }

    private function parse_route () {
        $uri = explode("?", $_SERVER["REQUEST_URI"], 2);
        if (preg_match("~index\.php(/.*)$~", $uri[0], $match)) {
            return explode('/', $match[0]);
        }
        return array();
    }

    // TODO: this is ridiculous
    const OUTPUTMODE_TEMPLATE = 0;
    const OUTPUTMODE_JSON = 1;
    const OUTPUTMODE_NORMAL = 2;
    const OUTPUTMODE_PLAIN = 3;
    const OUTPUTMODE_CSS = 4;
    const OUTPUTMODE_JAVASCRIPT = 5;
    const OUTPUTMODE_RSS = 6;
    private $outputmode = self::OUTPUTMODE_TEMPLATE;
    public function setOutputMode ($mode) {
        $this->outputmode = $mode;
    }

    protected function output () {
        switch ($this->outputmode) {
            case self::OUTPUTMODE_TEMPLATE:
                header("Content-type: text/html");
                $view = new Template('master');
                $view->load_data(array(
                    'title' => $this->config->get('title'),
                    'description' => $this->config->get('description'),
                    'content' => $this->app_output,
                    'navigation' => $this->navi->render()
                ));
                $view->render();
                break;
            case self::OUTPUTMODE_JSON:
                header("Content-type: application/json");
                echo $this->app_output;
                break;
            case self::OUTPUTMODE_CSS:
                header("Content-type: text/css");
                echo $this->app_output;
                break;
            case self::OUTPUTMODE_JAVASCRIPT:
                header("Content-type: application/javascript");
                echo $this->app_output;
                break;
            case self::OUTPUTMODE_RSS:
                header("Content-type: application/rss+xml");
                echo $this->app_output;
                break;
            case self::OUTPUTMODE_PLAIN:
                header("Content-type: text/plain");
            case self::OUTPUTMODE_NORMAL:
                echo $this->app_output;
                break;
            default:
                throw new Exception("Unknown output mode ({$this->outputmode})");
                echo $this->app_output;
                break;
        }
    }

    public function show_error ($details = array()) {
        header($_SERVER['SERVER_PROTOCOL'] . " " . $details['status']);
        $this->app_output = (new Template("error/generic.php", $details))->render(true);
        $this->output();
        exit;
    }

    public function show_404 () {
        return $this->show_error(array(
            "status" => 404,
            "statusmsg" => "Not Found",
            "title" => "404 - Not Found",
            "text" => "The requested page cannot be found"
        ));
    }

    public function show_403 () {
        return $this->show_error(array(
            "status" => 403,
            "statusmsg" => "Forbidden",
            "title" => "403 - Forbidden",
            "text" => "The requested content cannot be accessed"
        ));
    }

    public function show_500 () {
        return $this->show_error(array(
            "status" => 500,
            "statusmsg" => "",
            "title" => "Unrecoverable server error",
            "text" => "Something went terribly wrong"
        ));
    }

}