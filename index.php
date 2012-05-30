<?
require __DIR__ . '/OAI.php';

if (!$_GET['server']) {
	require __DIR__ . '/services.html.php';
	exit();
}

$oai = new OAI($_GET['server']);
$oai->generate();

