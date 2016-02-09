<?php

abstract class Controller {
    public function __construct () {}
    public function index () { App()->show_404(); }
}