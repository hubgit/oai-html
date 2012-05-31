<?php

if (PHP_SAPI !== 'cli') exit();

// configuration
$base = 'http://www.pubmedcentral.gov/oai/oai.cgi';
$set = 'bmcbiology';

$dir = 'data/items';
if (!file_exists($dir)) mkdir($dir, 0700, true);

// OAI class
require '../OAI.php';
$oai = new OAI($base);

// fetch all the items
$token = null;
$i = 1;
do {
	$file = $dir . '/' . $i++ . '.js.gz';
	print $file . "\n";
	list($url, $items, $token) = $oai->items($set, $token);
	file_put_contents('compress.zlib://' . $file, json_encode($items));
} while ($token);
