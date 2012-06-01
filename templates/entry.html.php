<h3><a rel="item" href="./?server=<? h($base); ?>&amp;id=<? h(urlencode($entry['id'])); ?>"><? h($entry['dc']['title'][0]); ?></a></h3>

<? if ($entry['dc']): ?>
<table vocab="http://purl.org/dc/elements/1.1/" resource="<? h($base . '#' . $entry['id']); ?>">
<? foreach ($entry['dc'] as $field => $values): ?>
    <tr>
        <th><? h($field); ?></th>
        <td><? foreach ($values as $value): ?><span property="<? h($field); ?>"><? h($value); ?></span><? endforeach; ?></td>
    </tr>
<? endforeach; ?>
</table>
<? endif; ?>
