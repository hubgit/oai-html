<?php

$dom = new DOMDocument;

$file = __DIR__ . '/doar.xml';

// wget 'http://www.opendoar.org/api13.php?oai=y' -O 'doar.xml'
//if (!file_exists($file)) file_put_contents($file, file_get_contents('http://www.opendoar.org/api13.php?oai=y'));

$dom->load($file);
$xpath = new DOMXPath($dom);
$nodes = $xpath->query('repositories/repository');

$items = array();

foreach ($nodes as $node) {
	$urlNodes = $xpath->query('rOaiBaseUrl', $node);
	if (!$urlNodes->length) continue;

	$url = $urlNodes->item(0)->textContent;
	if (!$url) continue;

	$items[] = array(
		'url' => $url,
		'name' => $xpath->query('rName', $node)->item(0)->textContent,
		'description' => $xpath->query('rDescription', $node)->item(0)->textContent,
	);
}

?>

<style>body { font-family: sans-serif }</style>

<h1>OAI Explorer</h1>

<form>
	<label>OAI Service Provider Base URL: <input name="server" type="url" size="100"></label>
	<input type="submit" value="Show">
</form>

<div>or choose a service provider from the list below:</div>

<ul>
<? foreach ($items as $item): ?>
	<li>
		<div><a rel="service" href="./?server=<? h($item['url']) ?>"><? h($item['name'] ? $item['name'] : $item['url']) ?></a></div>
		<? if ($item['description']): ?><p><? h($item['description']); ?></p><? endif; ?>
	</li>
<? endforeach; ?>
</ul>