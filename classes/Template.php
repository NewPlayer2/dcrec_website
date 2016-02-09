<?php

class Template implements View {
    private $data;
    private $file;
    public $comments = true;

    public function __construct ($file, $data = array()) {
        $file = BASEPATH . "/templates/" . $file;
        if (!is_readable($file)) {
            $file = $file . ".php";
            if (!is_readable($file)) {
                throw new Exception("Cannot template ($file)");
            }
        }
        $this->file = $file;
        $this->data = array();
        $this->load_data($data);
    }

    public function load_data () {
        if (func_num_args() === 1) {
            $this->data = array_merge($this->data, func_get_arg(0));
            return;
        }
        if (func_num_args() === 2) {
            $this->data[func_get_arg(0)] = func_get_arg(1);
            return;
        }
        throw new Exception("Invalid arguments");
    }

    public function render ($return = false) {
        extract($this->data, EXTR_SKIP);
        ob_start();
        $template_basename = htmlspecialchars(basename($this->file));
        if ($this->comments) {
            echo("\n<!-- template:$template_basename/ -->\n");
        }
        include($this->file);
        if ($this->comments) {
            echo("\n<!-- /template:$template_basename -->\n");
        }
        $html = ob_get_clean();
        if ($return) {
            return $html;
        }
        echo $html;
    }
}