<?php

namespace Drupal\geocoder_geofield\Plugin\Geocoder\Dumper;

use Drupal\geocoder\DumperBase;

/**
 * Provides a GPX geocoder dumper plugin.
 *
 * @GeocoderDumper(
 *   id = "gpx",
 *   name = "GPX",
 *   handler = "\Geocoder\Dumper\Gpx"
 * )
 */
class Gpx extends DumperBase {}
