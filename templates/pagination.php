<nav class="nav-pagination">
  <?php if (current($pages) > 1): ?>
  <ul class="pagination">
  <li class="normal skip"><a href="<?=htmlspecialchars($baseurl."/1")?>">1 ... </a></li>
  </ul>
  <?php endif; ?>

  <ul class="pagination">
    <li class="<?=($current <= 1 ? "disabled" : "normal")?>">
    <?php if ($current > 1) : ?><a href="<?=htmlspecialchars($baseurl . "/" . ($current - 1))?>" aria-label="Previous"><?php endif; ?>
        <span aria-hidden="true">&laquo;</span>
    <?php if ($current > 1) : ?></a><?php endif; ?>
    </li>

    <?php foreach ($pages as $p) : ?>
    <?php if ($p) : ?>
    <li class="<?=($p == $current ? "active" : "normal")?>"><a href="<?=htmlspecialchars($baseurl."/".$p)?>"><?=htmlspecialchars($p)?></a></li>
    <?php endif; ?>
    <?php endforeach; ?>

    <li class="<?=($current >= end($pages) ? "disabled" : "normal")?>">
    <?php if ($current < end($pages)) : ?><a href="<?=htmlspecialchars($baseurl . "/" . ($current + 1))?>" aria-label="Next"><?php endif; ?>
        <span aria-hidden="true">&raquo;</span>
    <?php if ($current < end($pages)) : ?></a><?php endif; ?>
    </li>

  </ul>

  <?php if (end($pages) < $max): ?>
  <ul class="pagination">
  <li class="normal skip"><a href="<?=htmlspecialchars($baseurl."/".$max)?>"> ... <?=htmlspecialchars($max)?></a></li>
  </ul>
  <?php endif; ?>
</nav>