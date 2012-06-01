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

require __DIR__ . '/templates/services.html.php';