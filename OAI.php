<?php

/**
 * Interact with an OAI-PMH repository
 */
class OAI {
  /** @var string The base URL of the repository */
  public $base;

  /** @var resource cURL */
  public $curl;

  /**
   * Create a new instance.
   * @param string $base base URL of the repository
   */
  function __construct($base) {
    $this->base = $base;
    $this->curl = curl_init();
    curl_setopt_array($this->curl, array(
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => 'gzip,deflate',
      CURLOPT_HTTPHEADER => array('Acccept' => 'application/xml'),
    ));
  }

  /**
   * A description of the OAI-PMH repository.
   * @return array
   */
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

  /**
   * A paginated list of all sets in the repository.
   * @param string|null $token
   * @return array
   */
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

    return array($url, $items, $this->token($xpath, $root));
  }

  /**
   * A paginated list of all items in a set.
   * @param string $set
   * @param string|null $token
   * @param string|null $from
   * @param string|null $until
   * @return array
   */
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

    return array($url, $items, $this->token($xpath, $root));
  }

  /**
   * Metadata for a specific item.
   * @param string $id
   * @return array
   */
  function item($id) {
    $params = array(
      'verb' => 'GetRecord',
      'metadataPrefix' => 'oai_dc',
      'identifier' => $id,
    );

    list($url, $xpath, $root) = $this->fetch($params);

    $record = $xpath->query('oai:record', $root)->item(0);
    $item = $this->metadata($xpath, $record);

    return array($url, $item);
  }

  /**
   * Call the OAI-PMH interface.
   * Used internally by this class, but can be called directly to get the response XML.
   * @param array $params
   * @param string|null $token resumptionToken
   * @return array
   */
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

    return array($url, $xpath, $root, $dom);
  }

  /**
   * Parse a resumption token from the response.
   * @param DOMXPath $xpath
   * @param DOMElement $root
   * @return string|null
   */
  private function token($xpath, $root) {
    $tokenNodes = $xpath->query('oai:resumptionToken', $root);
    return $tokenNodes->length ? $tokenNodes->item(0)->textContent : null;
  }

  /**
   * Parse metadata for a specific item into an array.
   * Currently only reads Dublin Core metadata.
   * @param DOMXPath $xpath
   * @param DOMElement $record
   * @return array
   */
  private function metadata($xpath, $record) {
    $item = array(
      'id' => $xpath->query('oai:header/oai:identifier', $record)->item(0)->textContent,
      'dc' => array(),
    );

    foreach ($xpath->query('oai:metadata/oai_dc:dc/*', $record) as $node) {
      $item['dc'][$node->localName][] = $node->textContent;
    }

    return $item;
  }
}