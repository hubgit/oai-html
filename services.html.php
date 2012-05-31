<?php

$dom = new DOMDocument;
@$dom->loadHTMLFile(__DIR__ . '/providers.html');

$xpath = new DOMXPath($dom);
$nodes = $xpath->query('//table[2]/tr');

$items = array();
foreach ($nodes as $node) {
	$items[] = array(
		'url' => $xpath->query('td[4]', $node)->item(0)->textContent,
		'name' => $xpath->query('td[3]', $node)->item(0)->textContent,
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
	<li><a rel="service" href="./?server=<? h($item['url']) ?>"><? h($item['name'] ? $item['name'] : $item['url']) ?></a></li>
<? endforeach; ?>
</ul>