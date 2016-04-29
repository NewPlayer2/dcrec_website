<?php

class search extends Controller {

    public function index () {
        if (!empty($_POST)) {
            return $this->results();
        } else {
            return $this->form();
        }
    }

    public function form () {
        $view = new Template('search/search');
        $view->load_data('servers', Navigation::$servers);
        $view->load_data('backend', $this->app->site_url('search/results/'));
        $view->render();
    }

    private function run ($args) {
        $argfields = array("servers", "notservers", "players", "notplayers");
        foreach ($argfields as $key) {
            if (!isset($args[$key]) || !$args[$key]) {
                $args[$key] = array();
            }
        }

        $params = array();
        $sql = "";
        $sql_where = array();

        $cb4player = function ($phrase) use (&$params) {

            if ($phrase[0] === '"' && $phrase[strlen($phrase)-1] === $phrase[0]) {
                $phrase = str_replace("%", "\\%", substr($phrase, 1, -1));
            }
            $param = uniqid(":");
            $params[$param] = $phrase;
            return "player.name LIKE " . $param;
        };

        $sql = "SELECT SQL_CALC_FOUND_ROWS DISTINCT g.* FROM game AS g ";

        foreach ($args['players'] as $player) {
            if (!$player) continue;
            
            $tbl = uniqid("tbl");
            $sql .= "INNER JOIN (SELECT DISTINCT game AS gid FROM player ".
                    "WHERE " . implode(" OR ", array_map($cb4player, $player)) .
                    ") AS {$tbl} ON {$tbl}.gid = g.id ";
        }

        foreach (array_values($args['notplayers']) as $key => $player) {
            if (!$player) continue;
            
            $alias = "exclude" . $key;
            $sql .= "LEFT OUTER JOIN (SELECT DISTINCT game AS gid FROM player ".
                    "WHERE " . implode(" OR ", array_map($cb4player, $player)) .
                    ") AS {$alias} ON {$alias}.gid = g.id ";
            $sql_where[] = $alias . ".gid IS NULL ";
        }

        $cb4srvid = function ($srvid) use (&$params) {
            $param = uniqid(":");
            $params[$param] = $srvid;
            return "server.id = " . $param;
        };


        $srvids = array_filter(array_map("intval", $args["servers"]));
        $notsrvids = array_filter(array_map("intval", $args["notservers"]));
        $sql_srvids = implode(" OR ", array_map($cb4srvid, $srvids));
        $sql_notsrvids = implode(" OR ", array_map($cb4srvid, $notsrvids));

        $sql .= "RIGHT OUTER JOIN server ON server.id = g.server " .
            ($sql_srvids ? " AND (" . $sql_srvids . ")" : "")  .
            ($sql_notsrvids ? " AND NOT (" . $sql_notsrvids . ")" : "");

        $sql .= "WHERE g.id IS NOT NULL";
        if ($sql_where) {
            $sql .= " AND " . implode (" AND ", $sql_where);
        }

        $sql .= " ORDER BY g.endtime DESC ";

        if (isset($args['max'])) {
            $sql .= " LIMIT ";
            if (isset($args['offset'])){
                $sql .= ":offset, ";
                $params[":offset"] = $args["offset"];
            }
            $sql .= ":max";
            $params[":max"] = $args["max"];
        }

        $db = Database::get_instance();
        $sth = $db->prepare($sql);

        foreach ($params as $key => $value) {
            $sth->bindValue($key, $value, (is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR));
        }

        $sth->execute();

        return (object) array(
            "sth" => $sth,
            "total" => (int) $db->query("SELECT FOUND_ROWS() AS t")->fetch()->t
        );
    }

    public function results ($storedrequest = false, $page = 1) {
        session_start();
        // this is still really ugly :D
        if (empty($_POST)) {
            if ($storedrequest && isset($_SESSION['posts'][$storedrequest])) {
                $_POST = $_SESSION['posts'][$storedrequest];
            } else {
                $this->app->redirect("search");
            }
        }

        if (!$storedrequest) {
            $storedrequest = uniqid();
            $_SESSION['posts'][$storedrequest] = $_POST;
        }

        $this->form();

        $players = array();
        $notplayers = array();
        foreach ($_POST['played'] as $index => $played) {
            if ($played) {
                $players[] = $_POST['player'][$index];
            } else {
                $notplayers[] = $_POST['player'][$index];
            }
            // array_push(($played ? $players : $notplayers), $_POST['player'][$index]); // o_O
        }

        $results = $this->run(array(
            "max" => Game::$pagesize,
            "offset" => ($page - 1) * Game::$pagesize,
            "players" => $players,
            "notplayers" => $notplayers,
            "servers" => array_filter(array_map("intval", $_POST['servers'])),
            "notservers" => array_filter(array_map("intval", $_POST['notservers']))
        ));

        $resultview = new Template('gamelist', array(
            'list' => $results->sth->fetchAll(PDO::FETCH_CLASS, "Game"),
            'pagination' => gamelist::pagination(
                $page,
                $results->total,
                $this->app->site_url("search/results/{$storedrequest}")
            )
        ));

        $resultview->render();
    }

    public function style () {
        $this->app->setOutputMode(Application::OUTPUTMODE_CSS);
        $css = new Template('search/search.css');
        $css->comments = false;
        $css->render();
    }

    public function script () {
        $this->app->setOutputMode(Application::OUTPUTMODE_JAVASCRIPT);
        $js = new Template('search/search.js');
        $js->comments = false;
        $js->render();
    }

}