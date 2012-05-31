<?

ini_set('display_errors', true);

ob_start();

require __DIR__ . '/OAI.php';

if (!$_GET['server']) {
	include __DIR__ . '/services.html.php';
	exit();
}

$oai = new OAI($_GET['server']);

$item = array();
$items = array();
$links = array();

if (isset($_GET['id'])) {
	list($item, $url) = $oai->item($_GET['id']);
}
else if (isset($_GET['set'])) {
	list($items, $url, $token) = $oai->items($_GET['set'], $_GET['resumptionToken'], $_GET['from'], $_GET['until']);
}
else {
	$info = $oai->identify();
	list($sets, $url, $token) = $oai->sets($_GET['resumptionToken']);
}

$links = array('alternate' => $url);
if ($token) $links['next'] = resumptionURL($token);

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

/**
 * Build the "next page" URL
 */
function resumptionURL($token) {
  $params = array_merge($_GET, array('resumptionToken' => $token));
  return './?' . http_build_query($params);
}