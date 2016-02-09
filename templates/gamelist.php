<?php if(!isset($pagination)): $pagination = ""; endif; ?>

<div class="gamelist-wrapper">

<?php if (empty($list)): ?>

    <p class="text-xl">No games to show</p>

<?php else: ?>

    <?=$pagination?>

    <div class="gamelist">
    <?php foreach ($list as $item) : ?>
    <?php $item->render(); ?>
    <?php endforeach; ?>
    </div>

    <?=$pagination?>

<?php endif; ?>

</div>