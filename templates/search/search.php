<?php $app = App(); ?>
<style>
@import url("<?=$app->site_url('search/style')?>");
</style>

<script src="<?=$app->site_url('search/script')?>"></script>

<div class="container">

  <div class="row">
  <div class="col-md-8">
    <h1>Search</h1>
    <form class="form-horizontal" role="form" method="post" action="<?=$backend?>">
        <?php $players = (!isset($_POST['player']) || empty($_POST['player']) ? array(0 => "") : $_POST['player']); ?>
        <?php foreach ($players as $playerindex => $aliases) : ?>
            <?php
            $playerindex = (int) $playerindex;
            $played = (!isset($_POST['player'][$playerindex]) || $_POST['played'][$playerindex]);
            ?>

            <div class="form-group player-condition <?=($playerindex ? "" : "original")?>">
                <label>
                    <span>Player set </span>
                    <button type="button" class="btn btn-xs btn-default remove-condition">
                        <span class="glyphicon glyphicon-remove text-danger" aria-hidden="true"></span>Remove
                    </button>
                </label>
                <div class="row">
                  <div class="col-xs-3">
                      <select name="played[<?=$playerindex?>]" class="form-control played-status">
                          <option value="1" <?=($played  ? "selected" : "")?>>Played</option>
                          <option value="0" <?=(!$played ? "selected" : "")?>>Did not play</option>
                      </select>
                  </div>
                  <div class="col-xs-9 player-name">
                  <?php if (!is_array($aliases)) { $aliases = array(""); } ?>

                  <?php foreach ($aliases as $aliasindex => $alias): ?>
                      <div class="row<?=($aliasindex === 0 ? " original" : "")?>">
                          <div class="col-xs-9">
                          <input name="player[<?=$playerindex?>][]" class="form-control" type="text" value="<?=htmlspecialchars($alias)?>"/>
                          </div>
                          <div class="col-xs-3">
                          <button class="btn btn-xs btn-default add-alias" type="button"><span class="glyphicon glyphicon-plus"></span>alt form (OR)</button>
                          <button class="btn btn-xs btn-default remove-alias" type="button"><span class="glyphicon glyphicon-remove"></span>Remove</button>
                          </div>
                      </div>
                  <?php endforeach; ?>
                  </div>
                </div>
            </div>
        <?php endforeach; ?>

        <div class="form-group">
        <p><button class="add-player btn btn-default btn-xs" type="button"><span class="glyphicon glyphicon-plus"></span>Add player set</button></p>
        </div>



        <?php
        if (isset($_POST['servers'])) { $_POST['servers'] = array_filter($_POST['servers']); }
        if (isset($_POST['notservers'])) { $_POST['notservers'] = array_filter($_POST['notservers']); }
        $serverfilters = (isset($_POST['servers']) && !empty($_POST['servers']));
        $serverexcludes = (isset($_POST['notservers']) && !empty($_POST['notservers']));
        ?>

        <div class="form-group">
        <label data-toggle="collapse" data-target="#serverfilter-collapsible"><a href="javascript:void(0);"><span class="glyphicon glyphicon-collapse-down"></span>Filter by server</a></label>
        <div class="collapse <?=($serverfilters ? "collapse-initially-open" : "")?>" id="serverfilter-collapsible">
        <p class="help-block">You can select multiple servers. Select the first option or leave unselected to search games played on any servers.</p>
        <select id="server-filter" class="form-control" name="servers[]" multiple>
        <option value="0" <?=(!$serverfilters ? "selected" : "")?>>-- Any server --</option>
        <?php foreach ($servers as $serverid => $server): ?>
            <option value="<?=(int)$serverid?>" <?=(isset($_POST['servers']) && in_array($serverid, $_POST['servers']) ? "selected" : "")?>>
            <?=htmlspecialchars($server)?>
            </option>
        <?php endforeach; ?>
        </select>
        </div>
        </div>

        <div class="form-group">
        <label data-toggle="collapse" data-target="#exclude-collapsible"><a href="javascript:void(0);"><span class="glyphicon glyphicon-collapse-down"></span>Exclude servers</a></label>
        <div class="collapse <?=($serverexcludes ? "collapse-initially-open" : "")?>" id="exclude-collapsible">
        <p class="help-block">Select server(s) to exclude from your search.</p>
        <select id="exclude-server-filter" class="form-control" name="notservers[]" multiple>
        <option value="0" <?=(!$serverexcludes ? "selected" : "")?>>-- No exclusions --</option>
        <?php foreach ($servers as $serverid => $server): ?>
            <option value="<?=(int)$serverid?>" <?=(isset($_POST['notservers']) && in_array($serverid, $_POST['notservers']) ? "selected" : "")?>>
            <?=htmlspecialchars($server)?>
            </option>
        <?php endforeach; ?>
        </select>
        </div>
        </div>

        <div>
        <button type="submit" class="btn btn-primary btn-lg"><span class="glyphicon glyphicon-search" aria-hidden="true"></span>Search</button>
        </div>

    </form>
  </div>
  <div class="col-md-4" style="border-left: 1px solid #eee;">
    <h2>How to search?</h2>
    <p>By default, the player name search phrase is matched as is. However, the <mark>%</mark> character can be used as a wildcard.</p>
    <p>The search for <mark class="kw">Ne%yer</mark> would match games with <span class="kw">NewPlayer</span> as well as <span class="kw">New Freaking Player</span> or <span class="kw">Nerdy annoyer</span>.</p>
    <p>If you need to search for player names containing the wildcard character itself, wrap your keywords with double quotes (<mark>"</mark>). For example: the search phrase <mark class="kw">100%efficiency</mark> <strong>would</strong> match a player named <span class="kw">100%<strong>in</strong>efficiency</span> but <mark class="kw">"100%efficiency"</mark> <strong>would not</strong>.</p>
    </div>
  </div>

</div>

<hr />