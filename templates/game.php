<div class="col-xs-12 col-sm-6 col-md-4 col-lg-3 gamelistitem-col">
    <div class="gamelistitem panel panel-default">
      <div class="panel-heading">
        <div class="direct-link-wrapper">
            <a class="direct-link" href="<?=htmlspecialchars($link)?>">Link</a>
        </div>
        <h3 class="panel-title"><?=htmlspecialchars($title)?></h3>
      </div>
      <div class="panel-body">
        <p class="ago"><?=htmlspecialchars(time_elapsed_string($endtime))?></p>
        <dl class="clearfix">
            <dt>Start</dt><dd><?=htmlspecialchars($starttime)?></dd>
            <dt>End</dt><dd><?=htmlspecialchars($endtime)?></dd>
            <dt>Duration</dt><dd><?=htmlspecialchars($duration)?></dd>
        </dl>
        <table class="table table-striped table-condensed table-bordered">
            <thead>
                <tr><th>Player</th><th>Territory</th><th>Score</th></tr>
            </thead>
            <tbody>
                <?php foreach ($players as $p): ?><tr>
                <td><?=htmlspecialchars(utf8_encode($p->name))?></td>
                <td><?=htmlspecialchars(Game::$terrs[$p->territory])?></td>
                <td><?=htmlspecialchars($p->score)?></td>
                </tr><?php endforeach; ?>
            </tbody>
        </table>

        <div class="buttons">
        <?php if ($dcrec_href) : ?>
            <a class="btn btn-default" href="<?=htmlspecialchars($dcrec_href)?>">Download recording</a>
        <?php endif; ?>
        </div>
      </div>
    </div>
</div>