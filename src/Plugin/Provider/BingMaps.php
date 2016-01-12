<?php
/**
 * @file
 * The BingMaps plugin.
 */

namespace Drupal\geocoder\Plugin\Provider;

use Drupal\geocoder\GeocoderProvider\GeocoderProvider;
use Geocoder\Geocoder;
use Geocoder\Provider\Provider;

/**
 * Class BingMaps.
 *
 * @GeocoderProviderPlugin(
 *  id = "BingMaps",
 *  arguments = {
 *    "@geocoder.http_adapter"
 *  }
 * )
 */
class BingMaps extends GeocoderProvider {
  /**
   * @inheritdoc
   */
  public function init() {
    $configuration = $this->getConfiguration();
    $this->setHandler(new \Geocoder\Provider\BingMaps($this->getAdapter(), $configuration['apiKey']));

    return parent::init();
  }

}
