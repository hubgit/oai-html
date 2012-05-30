<?

ini_set('display_errors', true);

require __DIR__ . '/OAI.php';

if (!$_GET['server']) {
	include __DIR__ . '/services.html.php';
	exit();
}

$oai = new OAI($_GET['server']);
$oai->generate();
