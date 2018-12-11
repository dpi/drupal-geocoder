<?php

namespace Drupal\geocoder\Plugin\Geocoder\Provider;

use Drupal\geocoder\ProviderUsingHandlerBase;

/**
 * Provides a File geocoder provider plugin.
 *
 * @GeocoderProvider(
 *   id = "gpxfile",
 *   name = "GPXFile",
 *   handler = "\Drupal\geocoder\Geocoder\Provider\GPXFile"
 * )
 */
class GPXFile extends ProviderUsingHandlerBase {}
