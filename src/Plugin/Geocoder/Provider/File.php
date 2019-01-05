<?php

namespace Drupal\geocoder\Plugin\Geocoder\Provider;

use Drupal\geocoder\ConfigurableProviderUsingHandlerBase;

/**
 * Provides a File geocoder provider plugin.
 *
 * @GeocoderProvider(
 *   id = "file",
 *   name = "File",
 *   handler = "\Drupal\geocoder\Geocoder\Provider\File",
 *   arguments = {
 *     "filename" = ""
 *   }
 * )
 */
class File extends ConfigurableProviderUsingHandlerBase {}
