<?php

define("BASEPATH", dirname(__FILE__ ));
define("CLASSPATH", BASEPATH . "/classes");

/** copy paste from http://stackoverflow.com/questions/1416697/converting-timestamp-to-time-ago-in-php-e-g-1-day-ago-2-days-ago */
if (!function_exists("time_elapsed_string")) {
    function time_elapsed_string($datetime, $full = false) {
        $now = new DateTime;
        $ago = new DateTime($datetime);
        $diff = $now->diff($ago);

        $diff->w = floor($diff->d / 7);
        $diff->d -= $diff->w * 7;

        $string = array(
            'y' => 'year',
            'm' => 'month',
            'w' => 'week',
            'd' => 'day',
            'h' => 'hour',
            'i' => 'minute',
            's' => 'second',
        );
        foreach ($string as $k => &$v) {
            if ($diff->$k) {
                $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
            } else {
                unset($string[$k]);
            }
        }

        if (!$full) $string = array_slice($string, 0, 1);
        return $string ? implode(', ', $string) . ' ago' : 'just now';
    }
}

function __autoload ($classname) {
    $paths = array();
    if (substr($classname, -5) === "Model") {
        $paths[] = BASEPATH . "/models/" . basename($classname) . ".php";
    } else {
        $paths[] = CLASSPATH . "/" . basename($classname) . ".php";
    }
    $paths[] = BASEPATH . "/controllers/" . basename($classname) . ".php";
    foreach ($paths as $path) {
        if (is_readable($path)) {
            if (class_exists($classname)) {
                // ...
            } else {
                require_once($path);
            }
            return;
        }
    }
    throw new Exception("Class ($classname) autoload failed!");
}

function pre_r ($var, $ret = false) {
    $str = ("<pre class='pre_r'>" .
            htmlspecialchars(print_r($var, true)) .
            "</pre>");
    if ($ret) {
        return $str;
    }
    echo $str;
}

function App () {
    return Application::get_instance();
}

App()->execute();
