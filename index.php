<?

ini_set('display_errors', true);

ob_start();

require __DIR__ . '/OAI.php';

if (!$_GET['server']) {
	include __DIR__ . '/services.php';
	exit();
}

$oai = new OAI($_GET['server']);

$entry = array();
$entries = array();
$links = array();

if (isset($_GET['id'])) {
	list($url, $entry) = $oai->item($_GET['id']);
}
else if (isset($_GET['set'])) {
	list($url, $entries, $token) = $oai->items($_GET['set'], $_GET['resumptionToken'], $_GET['from'], $_GET['until']);
}
else {
	$info = $oai->identify();
	list($url, $sets, $token) = $oai->sets($_GET['resumptionToken']);
}

$links = array('alternate' => $url);
if ($token) $links['next'] = resumptionURL($token);

foreach ($links as $relation => $url) {
	header(sprintf('Link: <%s>; rel="%s"', $url, $relation));
}

$base = $oai->base;
require __DIR__ . '/templates/index.html.php';

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