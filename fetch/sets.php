<?php

if (PHP_SAPI !== 'cli') exit();

require '../oai-html/OAI.php';

$base = 'http://www.pubmedcentral.gov/oai/oai.cgi';

$oai = new OAI($base);

$data = array();

$token = null;
do {
	list($sets, $links, $token) = $oai->sets($token);
	$data = array_merge($data, $sets);
} while ($token);

header('Content-Type: application/json; charset=utf-8');
print json_encode($data);