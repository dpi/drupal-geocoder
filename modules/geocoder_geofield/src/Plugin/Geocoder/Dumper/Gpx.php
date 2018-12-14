<?php

namespace Drupal\geocoder_geofield\Plugin\Geocoder\Dumper;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\geocoder\DumperBase;
use Geocoder\Model\AddressCollection;
use Drupal\geofield\WktGenerator;

/**
 * Provides a GPX geocoder dumper plugin.
 *
 * @GeocoderDumper(
 *   id = "gpx",
 *   name = "GPX",
 *   handler = "\Drupal\geocoder_geofield\Geocoder\Dumper\Gpx"
 * )
 */
class Gpx extends DumperBase {

  /**
   * The WKT Generator Service.
   *
   * @var \Drupal\geofield\WktGenerator
   */
  protected $wktGenerator;

  /**
   * Constructs a \Drupal\Component\Plugin\PluginBase object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\geofield\WktGenerator $wkt_generator
   *   The WKT Generator Service.
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    WktGenerator $wkt_generator
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->wktGenerator = $wkt_generator;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('geofield.wkt_generator')
    );
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
