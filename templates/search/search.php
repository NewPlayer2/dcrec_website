<?php $app = App(); ?>
<style>
@import url("<?=$app->site_url('search/style')?>");
</style>

<script src="<?=$app->site_url('search/script')?>"></script>

<div class="container">
  <div class="row">
  <div class="col-md-6">
    <form class="form-horizontal" role="form" method="post" action="<?=htmlspecialchars($app->site_url('search/results/1'))?>">
        <div class="form-group">
        <label for="filter">Server</label>
        <select class="form-control" name="server">
        <option value="0" selected>Any server</option>
        <?php foreach ($servers as $serverid => $server): ?>
        <option value="<?=(int)$serverid?>"><?=htmlspecialchars($server)?></option>
        <?php endforeach; ?>
        </select>
        </div>
        <?php if (!isset($players) || empty($players)) { $players = array(""); } ?>
        <?php foreach ($players as $idx => $playerpost) : ?>
            <div class="form-group player-condition <?=($idx ? "" : "original")?>">
                <label>
                    <span>Player </span>
                    <button type="button" class="btn btn-xs btn-default remove-condition">
                        <span class="glyphicon glyphicon-remove text-danger" aria-hidden="true"></span>Remove
                    </button>
                </label>
                <div class="row">
                <div class="col-xs-3 connective-col">
                    <select name="connective[]" class="form-control">
                        <option value="OR" selected>OR</option>
                        <option value="AND" class="disabled" disabled>AND</option>
                    </select>
                </div>
                <div class="col-xs-9">
                    <input name="player[]" class="form-control" type="text" value="<?=htmlspecialchars($playerpost)?>"/>
                </div>
                </div>
            </div>
        <?php endforeach; ?>
        <div class="help-block">
        <a class="add-player btn btn-default btn-xs" href="javascript:void(0);">Add a player</a>
        </div>
        <button type="submit" class="btn btn-primary btn-lg"><span class="glyphicon glyphicon-search" aria-hidden="true"></span>Search</button>
    </form>
    </div>
    <div class="col-md-5 col-md-push-1">
    <h3>How to search</h3>
    <p>By default, the player name search phrase is matched as is. However, the % character can be used as a wildcard.</p>
    <p>For example the search for <mark class="kw">Ne%yer</mark> would match games with <span class="kw">NewPlayer</span> as well as <span class="kw">New Freaking Player</span> or <span class="kw">Nerdy annoyer</span>.</p>
    <p>If you need to search for player names containing the wildcard character, wrap your keywords with double quotes ("). For example the search phrase <mark class="kw">100%efficiency</mark> <strong>would</strong> match a player named <span class="kw">100%<strong>in</strong>efficiency</span> but <mark class="kw">"100%efficiency"</mark> <strong>would not</strong>.</p>
    <p>Use the white button to add multiple player names to your search. Unfortunately using <span>AND</span> as a connective is currently disabled.</p>
    </div>

    </div>
</div>
<hr />
