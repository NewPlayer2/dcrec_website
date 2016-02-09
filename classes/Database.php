<?php
class Database extends Singleton {
    protected static $_instance = null;
    private $pdo;

    protected function __construct () {
        $conf = Config::get_instance()->load("database")->get('db');
        $dsn = $conf['driver'] . ":dbname=" . $conf['database'] . ";host=" . $conf['host'];
        $options = array(
           PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
           PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ
        );
        $this->pdo = new PDO($dsn, $conf['user'], $conf['pass'], $options);
    }

    public function foundRows () {
        return (int) $this->query("SELECT FOUND_ROWS() AS t")->fetch()->t;
    }

    public function __call ($name, $args) {
        return call_user_func_array(array($this->pdo, $name), $args);
    }
}
