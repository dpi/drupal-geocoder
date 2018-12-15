<?php

namespace Drupal\geocoder_geofield\Geocoder\Dumper;

use Geocoder\Dumper\Dumper;
use Geocoder\Model\AddressCollection;
use Geocoder\Model\Address;

/**
 * Dumper.
 */
class Geometry implements Dumper {

  /**
   * Dumper.
   *
   * @var \Geocoder\Dumper\Dumper
   */
  private $dumper;

  /**
   * The WKT Generator Service.
   *
   * @var \Drupal\geofield\WktGenerator
   */
  protected $wktGenerator;

  /**
   * Geophp interface.
   *
   * @var \Drupal\geofield\GeoPHP\GeoPHPInterface
   */
  private $geophp;

  /**
   * {@inheritdoc}
   */
  public function __construct() {
    $this->dumper = \Drupal::service('plugin.manager.geocoder.dumper')->createInstance('geojson');
    $this->wktGenerator = \Drupal::service('geofield.wkt_generator');
    $this->geophp = \Drupal::service('geofield.geophp');
  }

  /**
   * {@inheritdoc}
   */
  public function dump(Address $address) {
    return $this->geophp->load($this->dumper->dump($address), 'gpx');
  }

  /**
   * Generate and dump linestring.
   *
   * @param \Geocoder\Model\AddressCollection $address_collection
   *   The addresses collection.
   * @param string $format
   *   The output format.
   *
   * @return string
   *   The formatted address.
   */
  protected function dumpAddressCollectionLinestring(AddressCollection $address_collection, $format) {
    $addresses = $address_collection->all();
    $points = [];
    /* @var \Geocoder\Model\Address $address */
    foreach ($addresses as $address) {
      $points[] = [$address->getCoordinates()->getLongitude(), $address->getCoordinates()->getLatitude()];
    }
    $linestring = $this->wktGenerator->wktBuildLinestring($points);

    return $linestring;
  }

}
