<?php

class OAI {
  public $base;
  public $curl;

  function __construct($base) {
    $this->base = $base;
    $this->curl = curl_init();
    curl_setopt_array($this->curl, array(
      CURLOPT_URL => $url,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => 'gzip,deflate',
      CURLOPT_HTTPHEADER => array('Acccept' => 'application/xml'),
    ));
  }

  function fetch($params, $token = null) {
    if ($token) {
      $params = array(
        'verb' => $params['verb'],
        'resumptionToken' => $token,
      );
    }

    $url = $this->base . '?' . http_build_query($params);
    curl_setopt($this->curl, CURLOPT_URL, $url);
    $xml = curl_exec($this->curl);

    $dom = new DOMDocument;
    @$dom->loadXML($xml);

    $xpath = new DOMXPath($dom);
    $xpath->registerNamespace('oai', 'http://www.openarchives.org/OAI/2.0/');
    $xpath->registerNamespace('oai_dc', 'http://www.openarchives.org/OAI/2.0/oai_dc/');
    $xpath->registerNamespace('dc', 'http://purl.org/dc/elements/1.1/');

    $root = $xpath->query('oai:' . $params['verb'])->item(0);

    return array($url, $xpath, $root);
  }

  function identify() {
    $params = array('verb' => 'Identify');
    list($url, $xpath, $root) = $this->fetch($params);
    $xpath->registerNamespace('oai_id', 'http://www.openarchives.org/OAI/2.0/oai-identifier');

    return array(
      'name' => $xpath->query('oai:repositoryName', $root)->item(0)->textContent,
      'url' => $xpath->query('oai:baseURL', $root)->item(0)->textContent,
      'sample' => $xpath->query('oai:description/oai_id:oai-identifier/oai_id:sampleIdentifier', $root)->item(0)->textContent,
    );
  }

  function sets($token = null) {
    $params = array('verb' => 'ListSets');
    list($url, $xpath, $root) = $this->fetch($params, $token);

    $items = array();
    foreach ($xpath->query('oai:set', $root) as $set) {
      $items[] = array(
        'id' => $xpath->query('oai:setSpec', $set)->item(0)->textContent,
        'name' => $xpath->query('oai:setName', $set)->item(0)->textContent,
      );
    }

    return array($items, $url, $this->token($xpath, $root));
  }

  function items($set, $token = null, $from = null, $until = null) {
    $params = array(
      'verb' => 'ListRecords',
      'set' => $set,
      'metadataPrefix' => 'oai_dc',
      'from' => $from,
      'until' => $until,
    );

    list($url, $xpath, $root) = $this->fetch($params, $token);

    $items = array();
    foreach ($xpath->query('oai:record', $root) as $record) {
      $items[] = $this->metadata($xpath, $record);
    }

    return array($items, $url, $this->token($xpath, $root));
  }

  function item($id) {
    $params = array(
      'verb' => 'GetRecord',
      'metadataPrefix' => 'oai_dc',
      'identifier' => $id,
    );

    list($url, $xpath, $root) = $this->fetch($params);

    $record = $xpath->query('oai:record', $root)->item(0);
    $item = $this->metadata($xpath, $record);

    return array($item, $url);
  }

  private function token($xpath, $root) {
    $tokenNodes = $xpath->query('oai:resumptionToken', $root);
    if ($tokenNodes->length) {
      return $tokenNodes->item(0)->textContent;
    }
  }

  private function metadata($xpath, $record) {
    $item = array(
      'id' => $xpath->query('oai:header/oai:identifier', $record)->item(0)->textContent,
      'dc' => array(),
    );

    foreach ($xpath->query('oai:metadata/oai_dc:dc/*', $record) as $node) {
      $item['dc'][$node->localName] = $node->textContent;
    }

    return $item;
  }
}