<?php

abstract class Controller {
    public function __construct () {
        $this->app = App();
    }
    public function index () { $this->app->show_404(); }
}