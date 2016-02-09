<div class="dropdown">
  <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
    Choose a server
    <span class="caret"></span>
  </button>
  <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
  <?php foreach ($servers as $server) : ?>
    <li><a href="<?=App()->site_url("gamelist/server/".(int)$server->id)?>">
        <?=htmlspecialchars($server->name)?></a></li>
  <?php endforeach; ?>    
  </ul>
</div>