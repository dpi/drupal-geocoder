<?php

namespace Drupal\geocoder_image\Plugin\Geocoder\Provider;

use Drupal\geocoder\ProviderUsingHandlerBase;

/**
 * Provides an Image geocoder provider plugin.
 *
 * @GeocoderProvider(
 *   id = "image",
 *   name = "Image",
 *   handler = "\Drupal\geocoder_image\Geocoder\Provider\Image"
 * )
 */
class Image extends ProviderUsingHandlerBase {}
