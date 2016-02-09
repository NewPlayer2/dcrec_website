<nav class="navbar navbar-inverse">
  <div class="container-fluid">
    <!-- Brand and toggle get grouped for better mobile display -->
    <div class="navbar-header">
      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a class="navbar-brand" href="<?=App()->site_url()?>">'Muricon</a>
    </div>

    <!-- Collect the nav links, forms, and other content for toggling -->
    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
      <ul class="nav navbar-nav">
        <li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Choose server <span class="caret"></span></a>
          <ul class="dropdown-menu">
            <li><a href="<?=App()->site_url('gamelist/latest')?>">Latest games</a></li>
            <li role="separator" class="divider"></li>
            <?php foreach ($servers as $serverid => $server) : ?>
                <li>
                <a href="<?=App()->site_url("gamelist/server/".(int)$serverid)?>">
                <?=htmlspecialchars($server)?>
                </a>
                </li>
            <?php endforeach; ?>
          </ul>
        </li>

        <li><a href="<?=App()->site_url('search')?>">Search</a></li>

        <?php foreach ($extended as $href => $item): ?>
            <li><a href="<?=htmlspecialchars($href)?>"><?=htmlspecialchars($item)?></a></li>
        <?php endforeach; ?>
      </ul>

      <ul class="nav navbar-nav navbar-right">
        <li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">RSS feeds <span class="caret"></span></a>
          <ul class="dropdown-menu">
            <li><a href="<?=App()->site_url('gamelist/rss/latest')?>">100 Latest games</a></li>
            <li role="separator" class="divider"></li>
            <?php foreach ($servers as $serverid => $server) : ?>
                <li>
                <a href="<?=App()->site_url("gamelist/rss/server/".(int)$serverid)?>">
                <?=htmlspecialchars($server)?>
                </a>
                </li>
            <?php endforeach; ?>
          </ul>
        </li>
      </ul>

    </div><!-- /.navbar-collapse -->
  </div><!-- /.container-fluid -->
</nav>