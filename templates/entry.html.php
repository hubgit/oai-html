<h3><a rel="item" href="./?server=<? h($base); ?>&amp;id=<? h(urlencode($entry['id'])); ?>"><? h($entry['dc']['title'][0]); ?></a></h3>

<? if ($entry['dc']): ?>
<table vocab="http://schema.org/" typeof="CreativeWork" resource="<? h($entry['id']); ?>">
<? foreach ($entry['dc'] as $field => $values): ?>
    <tr>
        <th><? h($field); ?></th>
        <td><? foreach ($values as $value): ?><span property="dc:<? h($field); ?>"><? h($value); ?></span><? endforeach; ?></td>
    </tr>
<? endforeach; ?>
</table>
<? endif; ?>
