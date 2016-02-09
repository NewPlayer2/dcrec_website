<?php

$classes = array("alert");

$type = (isset($type) ? ($type === "error" ? "danger" : $type) : "info");
$message = (isset($message) ? $message : "");
$classes[] = "alert-".htmlspecialchars($type);

?>
<div class="<?=implode(" ", $classes)?>" role="alert">
  <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
  <span class="sr-only"><?=($type === "danger" ? "Error" : "Info")?></span>
  <?php echo htmlspecialchars($message); ?>

  <?php if (isset($continue) && $continue): ?>
    <p><a class='btn btn-sm btn-default' href="<?=App()->site_url($continue)?>">Continue</a></p>
  <?php endif; ?>
</div>
