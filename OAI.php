<?php

ini_set('display_errors', true);

function h($text) {
	print htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

class OAI {
	public $baseURL;

	function __construct($baseURL) {
		$this->baseURL = $baseURL;
	}

	function generate() {
		ob_start();

		$fields = array();
		$items = array();
		$links = array();

		if (isset($_GET['id'])) {
			list($fields, $links) = $this->item($_GET['id']);
		}
		else if (isset($_GET['set'])) {
			list($items, $links) = $this->items($_GET['set'], $_GET['resumptionToken'], $_GET['from'], $_GET['until']);
		}
		else {
			$info = $this->identify();
			list($sets, $links) = $this->sets($_GET['resumptionToken']);
		}

		foreach ($links as $relation => $url) {
			header(sprintf('Link: <%s>; rel="%s"', $url, $relation));
		}

		$baseURL = $this->baseURL;
		require __DIR__ . '/index.html.php';
		ob_end_flush();
	}

	function xpath($url) {
		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_ENCODING, 'gzip,deflate');
		$xml = curl_exec($curl);

		$dom = new DOMDocument;
		$dom->loadXML($xml);
		//print $dom->saveXML(); exit();

		$xpath = new DOMXPath($dom);
		$xpath->registerNamespace('oai', 'http://www.openarchives.org/OAI/2.0/');
		$xpath->registerNamespace('oai_dc', 'http://www.openarchives.org/OAI/2.0/oai_dc/');
		$xpath->registerNamespace('dc', 'http://purl.org/dc/elements/1.1/');
		return $xpath;
	}

	function resumptionURL($xpath, $root) {
		$tokenNodes = $xpath->query('oai:resumptionToken', $root);
		if ($tokenNodes->length) {
			$params = array_merge($_GET, array('resumptionToken' => $tokenNodes->item(0)->textContent));
			return './?' . http_build_query($params);
		}
	}

	function identify() {
		$params = array(
			'verb' => 'Identify',
		);

		$url = $this->baseURL . '?' . http_build_query($params);
		$xpath = $this->xpath($url);
		$root = $xpath->query('oai:' . $params['verb'])->item(0);

		$xpath->registerNamespace('oai_id', 'http://www.openarchives.org/OAI/2.0/oai-identifier');

		return array(
			'name' => $xpath->query('oai:repositoryName', $root)->item(0)->textContent,
			'url' => $xpath->query('oai:baseURL', $root)->item(0)->textContent,
			'sample' => $xpath->query('oai:description/oai_id:oai-identifier/oai_id:sampleIdentifier', $root)->item(0)->textContent,
		);
	}

	function sets($token = null) {
		$params = array(
			'verb' => 'ListSets',
			'resumptionToken' => $token,
		);

		$url = $this->baseURL . '?' . http_build_query($params);
		$xpath = $this->xpath($url);
		$root = $xpath->query('oai:' . $params['verb'])->item(0);

		$items = array();
		foreach ($xpath->query('oai:set', $root) as $set) {
			$items[] = array(
			  'id' => $xpath->query('oai:setSpec', $set)->item(0)->textContent,
			  'name' => $xpath->query('oai:setName', $set)->item(0)->textContent,
			  );
		}

		$links = array('alternate' => $url);
		$resumptionURL = $this->resumptionURL($xpath, $root);
		if ($resumptionURL) $links['next'] = $resumptionURL;

		return array($items, $links);
	}

	function items($set, $token = null, $from, $until) {
		$params = array(
			'verb' => 'ListRecords',
			'set' => $set,
			'metadataPrefix' => 'oai_dc',
			'from' => $from,
			'until' => $until,
		);

		if ($token) {
			$params =  array(
				'verb' => $params['verb'],
				'resumptionToken' => $token,
			);
		}

		$url = $this->baseURL . '?' . http_build_query($params);
		$xpath = $this->xpath($url);
		$root = $xpath->query('oai:' . $params['verb'])->item(0);

		$items = array();
		foreach ($xpath->query('oai:record', $root) as $item) {
			$header = $xpath->query('oai:header', $item)->item(0);
			$metadata = $xpath->query('oai:metadata/oai_dc:dc', $item)->item(0);

			$items[] = array(
				'id' => $xpath->query('oai:identifier', $header)->item(0)->textContent,
				'title' => $xpath->query('dc:title', $metadata)->item(0)->textContent,
				'date' => $xpath->query('dc:date', $metadata)->item(0)->textContent,
				'description' => $xpath->query('dc:description', $metadata)->item(0)->textContent,
			);
		}

		$links = array('alternate' => $url);
		$resumptionURL = $this->resumptionURL($xpath, $root);
		if ($resumptionURL) $links['next'] = $resumptionURL;

		return array($items, $links);
	}

	function item($id) {
		$params = array(
			'verb' => 'GetRecord',
			'metadataPrefix' => 'oai_dc',
			'identifier' => $id,
		);

		$url = $this->baseURL . '?' . http_build_query($params);

		$dom = new DOMDocument;
		$dom->load($url);

		$xpath = new DOMXPath($dom);
		$xpath->registerNamespace('oai', 'http://www.openarchives.org/OAI/2.0/');
		$xpath->registerNamespace('oai_dc', 'http://www.openarchives.org/OAI/2.0/oai_dc/');
		$xpath->registerNamespace('dc', 'http://purl.org/dc/elements/1.1/');

		$root = $xpath->query('oai:' . $params['verb'])->item(0);

		$fields = array();
		foreach ($xpath->query('oai:record/oai:metadata/oai_dc:dc/*', $root) as $node) {
			$fields[] = array(
				'field' => $node->localName,
				'value' => $node->textContent,
			);
		}

		$links = array('alternate' => $url);

		return array($fields, $links);
	}
}