<?php
class Config Extends Singleton {
    protected static $_instance = null;

    private $config = array();
    private static $loaded = array();

    public function load ($name, $force_reload = false) {
        $f = BASEPATH . "/config/" . $name . ".php";

        if (isset(self::$loaded[$f]) && !$force_reload) {
            return $this;
        }

        $config = array();

        require_once($f);
        $this->config = array_merge($this->config, $config);
        self::$loaded[$f] = true;
        return $this;
    }

    public function get ($item) {
        if (isset($this->config[$item])) {
            return $this->config[$item];
        }
        throw new Exception("Config fail");
    }

}