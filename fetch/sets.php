<?php

if (PHP_SAPI !== 'cli') exit();

$base = 'http://www.pubmedcentral.gov/oai/oai.cgi';

require '../OAI.php';
$oai = new OAI($base);

$data = array();

$token = null;
do {
	list($sets, $links, $token) = $oai->sets($token);
	$data = array_merge($data, $sets);
} while ($token);

print_r($data);