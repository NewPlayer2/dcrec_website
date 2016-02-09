<?php

class search extends Controller {

    public function index () {
        if (!empty($_POST)) {
            return $this->results();
        } else {
            return $this->form();
        }
    }

    public function form ($post = array()) {
        $view = new Template('search/search');
        $view->load_data('servers', Navigation::$servers);
        if (!empty($post)) {
            $view->load_data('players', $post['player']);
            $view->load_data('connective', $post['connective']);
        }
        $view->render();
    }

    /**
     * This implementation does not handle AND connectives between players.
     * TODO: rewrite the whole crap
     */
    public function results ($uniqid = false, $page = 1) {
        session_start();

        if (empty($_POST)) {
            if (isset($_SESSION['post'][$uniqid])) {
                $_POST = $_SESSION['post'][$uniqid];
            }
        } else {
            $uniqid = uniqid();
            $_SESSION['post'][$uniqid] = $_POST;
        }

        if (!empty(array_filter($_POST['player']))) {
            $params = array();

            $server_join = "";
            if (isset($_POST['server']) && $_POST['server']) {
                $params[":srv"] = $_POST['server'];
                $server_join .= "RIGHT OUTER JOIN server AS s " .
                                "ON s.id = g.server AND s.id = :srv ";
            }

            $where = array();
            foreach ($_POST['player'] as $idx => $kw) {
                $idx = (int) $idx; // injection attempts shall only fuck up the results
                if ($kw[0] === '"' && $kw[strlen($kw)-1] === '"') {
                    $kw = substr($kw, 1, -1);
                    $kw = str_replace("%", "\\%", $kw);
                }

                $param = ":like" . $idx;
                $where[] = " p.name LIKE " . $param;
                $params[$param] = $kw;
            }

            $sql = "SELECT SQL_CALC_FOUND_ROWS DISTINCT g.* " .
                   "FROM player AS p INNER JOIN game AS g ON g.id = p.game ".
                   $server_join .
                   "WHERE " . implode(" OR ", $where) .
                   " ORDER BY g.starttime DESC LIMIT :offset, :max";

            $db = Database::get_instance();
            $sth = $db->prepare($sql);
            // $params[':offset'] = ($page - 1) * Game::$pagesize;
            // $params[':max'] = Game::$pagesize;
            // $sth->execute($params); // LIMIT params need type info :/
            $sth->bindParam(':max', Game::$pagesize, PDO::PARAM_INT);
            $sth->bindValue(':offset', ($page - 1) * Game::$pagesize, PDO::PARAM_INT);
            foreach ($params as $key => $value) {
                $sth->bindValue($key, $value);
            }
            $sth->execute();
            $total = (int) $db->query("SELECT FOUND_ROWS() AS t")->fetch()->t;

            $resultview = new Template('gamelist', array(
                'list' => $sth->fetchAll(PDO::FETCH_CLASS, "Game"),
                'pagination' => gamelist::pagination($page, $total, App()->site_url("search/results/{$uniqid}"))
            ));

            $this->form($_POST);
            $resultview->render();

        } else {
            App()->redirect("search");
        }
    }

    public function style () {
        App()->setOutputMode(Application::OUTPUTMODE_CSS);
        $css = new Template('search/search.css');
        $css->comments = false;
        $css->render();
    }

    public function script () {
        App()->setOutputMode(Application::OUTPUTMODE_JAVASCRIPT);
        $js = new Template('search/search.js');
        $js->comments = false;
        $js->render();
    }

}