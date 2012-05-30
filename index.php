<?php

ini_set('display_errors', true);

function h($text) {
	print htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

require __DIR__ . '/OAI.php';

$oai = new OAI;

$fields = array();
$items = array();
$links = array();

if (isset($_GET['id'])) {
	list($fields, $links) = $oai->item($_GET['id']);
}
else if (isset($_GET['set'])) {
	list($items, $links) = $oai->items($_GET['set'], $_GET['resumptionToken'], $_GET['from'], $_GET['until']);
}
else {
	list($sets, $links) = $oai->sets($_GET['resumptionToken']);
}

foreach ($links as $relation => $url) {
	header(sprintf('Link: <%s>; rel="%s"', $url, $relation));
}

require __DIR__ . '/index.html.php';