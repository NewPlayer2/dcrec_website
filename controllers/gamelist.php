<?php

class gamelist extends Controller {

    public function index () {
        App()->redirect('gamelist/latest');
    }

    public function latest () {
        $view = new Template('gamelist');
        $view->load_data('list', Game::latest_each());
        $view->render();
    }

    public function server ($serverid, $page = false) {
        $page = ($page ? (int) $page : 1);
        $games = Game::gamelist($serverid, $page, Game::$pagesize);
        $view = new Template('gamelist');
        $view->load_data('list', $games['page']);
        $view->load_data('pagination',
            self::pagination(
                $page,
                $games['total'],
                App()->site_url("gamelist/server/" . (int)$serverid)
            )
        );
        $view->render();
    }

    public function rss () {
        $n = 100;
        app()->setOutputMode(Application::OUTPUTMODE_RSS);
        $args = func_get_args();
        $description = "";
        $games = array();
        switch ($args[0]) {
            case "server":
                $serverid = isset($args[1]) ? $args[1] : false;
                $page = isset($args[2]) ? $args[1] : false;
                $limit = $page ? null : $n;
                $games = Game::gamelist($serverid, $page, $limit)['page'];
                $description = "$n latest games on " . Navigation::$servers[$serverid];
                //$games = $games['page']; // dereferenging like above is not ok in php < 5.4
                break;
            case "latest":
            default:
                $games = Game::latest_n($n);
                $description = "$n latest games";
                break;
        }
        $view = new Template("rss_gamelist");
        $view->comments = false;
        $view->load_data('title', (isset($serverid) ?
            Navigation::$servers[$serverid] : app()->config->get('title')));
        $view->load_data('list', $games);
        $view->load_data('description', $description);
        $view->render();
    }

    public static function pagination ($page, $count, $baseurl) {
        $buttons = array();

        if ($page > 1) {
            $pages[] = $page - 1;
        }
        $show = 4;
        $max = ceil($count / Game::$pagesize);
        $first = floor($page - $show / 2);
        $first = ($first < 1 ? 1 : $first);
        $last = ceil($page + $show / 2);
        $last = ($last > $max ? $max : $last);

        $pag = new Template('pagination');

        $pag->load_data(array(
            'baseurl' => $baseurl,
            'current' => $page,
            'max' => $max,
            'pages' => range($first, $last)
        ));

        return $pag->render(true);
    }

}