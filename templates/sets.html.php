<h2>Sets</h2>

<ul>
<? foreach ($sets as $item): ?>
<li><a rel="set" href="./?server=<? h($base); ?>&amp;set=<? h(urlencode($item['id'])); ?>"><? h($item['dc']['name'] ? $item['dc']['name'] : $item['id']); ?></a></li>
<? endforeach; ?>
</ul>