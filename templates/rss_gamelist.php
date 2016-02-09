<?xml version="1.0" encoding="UTF-8"?>
<rss version="2.0">
  <channel>
    <title><?=(isset($title) ? $title : "")?> - RSS feed</title>
    <link><?=app()->current_url()?></link>
    <description><?=(isset($description) ? $description : "")?></description>
    <language>en-us</language>
    <ttl>15</ttl>
    <?php foreach ($list as $item) : ?>
    <?php $item->render_rss(); ?>
    <?php endforeach; ?>
  </channel>
</rss>


