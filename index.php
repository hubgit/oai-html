<?

//ini_set('display_errors', true);

ob_start();

require __DIR__ . '/OAI.php';

if (!$_GET['server']) {
	include __DIR__ . '/services.html.php';
	exit();
}

$oai = new OAI($_GET['server']);

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
	$info = $oai->identify();
	list($sets, $links) = $oai->sets($_GET['resumptionToken']);
}

foreach ($links as $relation => $url) {
	header(sprintf('Link: <%s>; rel="%s"', $url, $relation));
}

$base = $oai->base;
require __DIR__ . '/template.html.php';

ob_end_flush();

/** 
 * HTML-escaping helper function 
 */
function h($text) {
  print htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}