<?php

class Game {
    public static $db;
    public static $terrs = array("NA", "SA", "EU", "RU", "AS", "AF");

    public static $pagesize = 16;

    public static function latest_each () {
        $sql = "
            SELECT s.id           AS serverid,
                   s.name         AS servername,
                   g.*
            FROM   (SELECT server,
                           Max(id) AS id
                    FROM   game
                    GROUP  BY server) AS latest
                   INNER JOIN server s
                           ON latest.server = s.id
                   INNER JOIN game AS g
                           ON g.id = latest.id
            ORDER  BY g.starttime DESC";
        $sth = Database::get_instance()->query($sql);
        return $sth->fetchAll(PDO::FETCH_CLASS, __CLASS__);
    }

    public static function latest_n ($n = 100) {
        $sql = "
            SELECT s.id           AS serverid,
                   s.name         AS servername,
                   g.*
            FROM   game AS g
                   INNER JOIN server s
                           ON g.server = s.id
            ORDER  BY g.starttime DESC LIMIT :limit";
        $sth = Database::get_instance()->prepare($sql);
        $sth->bindParam(':limit', $n, PDO::PARAM_INT);
        $sth->execute();
        return $sth->fetchAll(PDO::FETCH_CLASS, __CLASS__);
    }

    public static function gamelist ($server, $page = false, $limit = null) {
        if (!self::$db) {
            self::$db = Database::get_instance();
        }

        $sql = "SELECT SQL_CALC_FOUND_ROWS g.* " .
               "FROM server AS s INNER JOIN game AS g ON g.server=s.id " .
               "WHERE s.id = :sid ORDER BY g.starttime DESC LIMIT :offset, :limit";

        $page = (int) $page;
        $limit = ($limit === null ? self::$pagesize : (int) $limit);
        $limit = ($limit ? $limit : "18446744073709551615"); // :D
        $offset = ($page ? ($page - 1) * self::$pagesize : 0);

        $sth = self::$db->prepare($sql);
        $sth->bindParam(':sid', $server, PDO::PARAM_INT);
        $sth->bindParam(':offset', $offset, PDO::PARAM_INT);
        $sth->bindParam(':limit', $limit, PDO::PARAM_INT);
        $sth->execute();

        return array(
            'total' => self::$db->foundRows(),
            'page' => $sth->fetchAll(PDO::FETCH_CLASS, __CLASS__)
        );
    }

    public static function getgame ($gameid) {
        self::dbconnect();
        $sql = "select * from game where id = :id";
        $sth = self::$db->prepare($sql);
        $sth->execute(array(':id' => (int)$gameid));
        return $sth->fetchObject( __CLASS__ );
    }

    private static function dbconnect () {
        if (!self::$db) {
            self::$db = Database::get_instance();
        }
        return self::$db;
    }

    private $id;
    private $starttime;
    private $endtime;
    private $server;
    private $eventlog_filename;
    private $eventlog_md5;
    private $dcrec_filename;
    private $dcrec_md5;
    private $dcrec_filesize;
    private $players;

    public function __construct () {
        self::dbconnect();
        $this->players = $this->getPlayers();
        $this->link = App()->site_url("singlegame/view/" . $this->id);

        $this->title = Navigation::$servers[$this->server];
        $replacements = app()->config->get('title_replacements');
        foreach ($replacements as $regexp => $replacement) {
            $this->title = preg_replace($regexp, $replacement, $this->title);
        }

        $timeformat = "H:i:s";
        $timediff = strtotime($this->endtime) - strtotime($this->starttime);
        $this->duration = gmdate($timeformat, $timediff);
        $this->dcrec_filesize = 0;

        if ($this->dcrec_readable()) {
            $this->dcrec_href = App()->site_url('singlegame/dcrec/'.$this->id);
            $basename = preg_replace("/\.gz$/", "", basename($this->dcrec_filename));
            $this->dcrec_href .= "/" . $basename;
        } else {
            $this->dcrec_href = false;
        }
    }

    public function dcrec_readable (&$out_filesize = NULL) {
        if (is_readable($this->dcrec_filename)) {
            $this->dcrec_filesize = filesize($this->dcrec_filename);
            $out_filesize = $this->dcrec_filesize;
            return true;
        }
        if (is_readable($this->dcrec_filename.".gz")) {
            $this->dcrec_filesize = 0;
            $out_filesize = 0;
            return true;
        }
        return false;
    }

    public function get_dcrec () {
        return $this->dcrec_filename;
    }
    
    public function getId () {
        return (int) $this->id;
    }

    private function getPlayers () {
        $sql = "select * from player where game = :g order by score desc";
        $sth = self::$db->prepare($sql);
        $sth->execute(array(':g' => $this->id));
        return $sth->fetchAll();
    }

    public function render () {
        (new Template('game', get_object_vars($this)))->render();
    }

    public function render_rss () {
        (new Template('game_rss', get_object_vars($this)))->render();
    }

    //public function __toString () {
    //    return $this->render();
    //}

}