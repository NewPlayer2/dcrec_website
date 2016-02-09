<?php

class singlegame extends Controller {

    public function index () {
        App()->redirect('gamelist/latest');
    }

    public function dcrec ($gameid, $filename = false) {
        $game = Game::getgame($gameid);
        $file = $game->get_dcrec();

        if ($filename && $filename !== basename($file)) {
            app()->show_404();
        }

        $gz = false;
        if (!is_readable($file)) {
            $file = $file.".gz";
            if (!is_readable($file)) {
                header($_SERVER['SERVER_PROTOCOL'] . ' 403');
                die("access denied");
            }
            $gz = true;
        }

        $patt = '/\.gz$/';
        $base = basename($file);
        //$gz = preg_match($patt, $file);
        $name = $gz ? preg_replace($patt, '', $base) : $base;
        header('Content-Description: Dedcon Recording');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="'.$name.'"');
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        if (!$gz) {
            header('Content-Length: ' . filesize($file));
        }
        ob_clean();
        flush();
        readgzfile($file);
        exit;
    }

    public function view ($gameid) {
        Game::getgame($gameid)->render();
    }

}