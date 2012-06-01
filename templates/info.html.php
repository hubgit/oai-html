<h2><a rel="base-url" href="<? h($info['url']); ?>"><? h($info['name']); ?></a></h2>

<? if ($info['sample']): ?>
  <div><a rel="sample-identifier" href="<? h($info['url']); ?>?verb=GetRecord&amp;metadataPrefix=oai_dc&amp;identifier=<? h(urlencode($info['sample'])); ?>">Sample Record</a></div>
<? endif; ?>