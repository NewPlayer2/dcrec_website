<?php
if (!isset($status)) {
    throw new Exception("Expected status code missing");
}

$pre_render_outputs = (isset($pre_render_outputs) ? $pre_render_outputs : false);
$title = (isset($title) ? $title : "Error " . $status);
$text = (isset($text) ? $text : false);
?>

<h1><?=$title?></h1>

<?php if($text): ?><p><?=htmlspecialchars($text)?></p><?php endif; ?>

<?php if($pre_render_outputs): ?>
<div class="well"><?=$pre_render_outputs?></div>
<?php endif; ?>
