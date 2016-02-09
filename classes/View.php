<?php
interface View {
    public function load_data ();
    public function render ($return = false);
}