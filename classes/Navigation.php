<?php

class Navigation extends Singleton {
    public static $servers = array();
    private $navi;
    public function __construct () {
        foreach (Database::get_instance()
          ->query("select * from server order by name asc")
          ->fetchAll() as $row) {
            self::$servers[$row->id] = $row->name;
        }

        $this->navi = new Template('navigation');
        $this->navi->load_data('servers', self::$servers);
        $this->navi->load_data('extended', array());
    }

    public function render () {
        return $this->navi->render(true);
    }
}