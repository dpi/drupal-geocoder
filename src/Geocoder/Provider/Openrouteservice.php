<?php

namespace Drupal\geocoder\Geocoder\Provider;

use Geocoder\Provider\Pelias\Pelias;
use Geocoder\Query\GeocodeQuery;
use Geocoder\Query\ReverseQuery;
use Http\Client\HttpClient;

/**
 * Adaptation of the official Pelias provider.
 *
 * For compatibility with the Openrouteservice API.
 */
class Openrouteservice extends Pelias {

  /**
   * The Openrouteservice API key.
   *
   * @var string
   */
  private $apiKey;

  /**
   * API root URL.
   *
   * @var string
   */
  private $root;

  /**
   * Constructs a new Openrouteservice provider.
   *
   * @param \Http\Client\HttpClient $client
   *   An HTTP adapter.
   * @param string $api_key
   *   The Openrouteservice API key.
   */
  public function __construct(HttpClient $client, string $api_key) {
    // We send fake root and version to the parent class and replace it when
    // needed: this is to avoid duplicate code, as parent $root is a private
    // property.
    parent::__construct($client, 'ROOT', 0);
    $this->root = 'https://api.openrouteservice.org/geocode';
    $this->apiKey = $api_key;
  }

  /**
   * {@inheritdoc}
   */
  protected function getGeocodeQueryUrl(GeocodeQuery $query, array $query_data = []): string {
    $query_data['api_key'] = $this->apiKey;
    return str_replace('ROOT/v0', $this->root, parent::getGeocodeQueryUrl($query, $query_data));
  }

  /**
   * {@inheritdoc}
   */
  protected function getReverseQueryUrl(ReverseQuery $query, array $query_data = []): string {
    $query_data['api_key'] = $this->apiKey;
    return str_replace('ROOT/v0', $this->root, parent::getReverseQueryUrl($query, $query_data));
  }

}
