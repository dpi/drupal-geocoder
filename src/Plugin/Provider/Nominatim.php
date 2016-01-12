<?php
/**
 * @file
 * The Nominatim plugin.
 */

namespace Drupal\geocoder\Plugin\Provider;

use Drupal\geocoder\GeocoderProvider\GeocoderProvider;
use Geocoder\Geocoder;
use Geocoder\Provider\Provider;

/**
 * Class Nominatim.
 *
 * @GeocoderProviderPlugin(
 *  id = "Nominatim",
 *  arguments = {
 *    "@geocoder.http_adapter"
 *  }
 * )
 */
class Nominatim extends GeocoderProvider {
  /**
   * @inheritdoc
   */
  public function init() {
    $configuration = $this->getConfiguration();
    $this->setHandler(new \Geocoder\Provider\Nominatim($this->getAdapter(), $configuration['rootUrl']));

    return parent::init();
  }

}
