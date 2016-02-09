<item>
  <?php if ($dcrec_href) : ?><enclosure url="<?=htmlspecialchars($dcrec_href)?>" length="<?=htmlspecialchars($dcrec_filesize)?>" type="application/octet-stream" /><?php endif; ?>
  <pubDate><?=htmlspecialchars(date('r', strtotime($endtime)))?></pubDate>
  <title><?=htmlspecialchars($title)?></title>
  <link><?=htmlspecialchars($link)?></link>
  <guid isPermaLink="true"><?=htmlspecialchars($link)?></guid>
  <description>
  <?php foreach ($players as $p): ?>
  Player (<?=rtrim(str_replace(';', '\;', htmlspecialchars(utf8_encode($p->name))), '\\')?>;<?=htmlspecialchars($p->score)?>;<?=htmlspecialchars(Game::$terrs[$p->territory])?>);
  <?php endforeach; ?>
  Start time: <?=htmlspecialchars($starttime)?>;
  End time: <?=htmlspecialchars($endtime)?>;
  Duration: <?=htmlspecialchars($duration)?>;</description>
</item>