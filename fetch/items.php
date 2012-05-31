<?php

if (PHP_SAPI !== 'cli') exit();

require '../oai-html/OAI.php';

$base = 'http://www.pubmedcentral.gov/oai/oai.cgi';
$set = 'bmcbiology';

$oai = new OAI($base);

$data = array();

$token = null;
do {
	list($items, $links, $token) = $oai->items($set, $token);
	$data = array_merge($data, $items);
} while ($token);

header('Content-Type: application/json; charset=utf-8');
print json_encode($data);