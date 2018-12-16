<?php

namespace Drupal\geocoder_geofield\Geocoder\Provider;

use Geocoder\Exception\NoResult;
use Geocoder\Exception\UnsupportedOperation;
use Geocoder\Provider\AbstractProvider;
use Geocoder\Provider\Provider;

/**
 * Provides a file handler to be used by 'gpxfile' plugin.
 */
class GPXFile extends AbstractProvider implements Provider {

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
    parent::__construct();
    $this->geophp = \Drupal::service('geofield.geophp');
  }

  /**
   * {@inheritdoc}
   */
  public function getName() {
    return 'gpxfile';
  }

  /**
   * {@inheritdoc}
   */
  public function geocode($filename) {
    $gpx_string = file_get_contents($filename);
    /* @var \Geometry|\GeometryCollection $geometry */
    $geometry = $this->geophp->load($gpx_string, 'gpx');
    if (!empty($geometry->components)) {
      return $geometry;
    }
    throw new NoResult(sprintf('Could not find GPX data in file: "%s".', basename($filename)));

  }

  /**
   * {@inheritdoc}
   */
  public function reverse($latitude, $longitude) {
    throw new UnsupportedOperation('The GPX plugin is not able to do reverse geocoding.');
  }

}
