<?php

namespace Drupal\geocoder\Plugin\Geocoder\Provider;

use Drupal\geocoder\ConfigurableProviderUsingHandlerWithAdapterBase;

/**
 * Provides an Openrouteservice geocoder provider plugin.
 *
 * @GeocoderProvider(
 *   id = "openrouteservice",
 *   name = "Openrouteservice",
 *   handler = "\Drupal\geocoder\Geocoder\Provider\Openrouteservice",
 *   arguments = {
 *     "api_key" = ""
 *   }
 * )
 */
class Openrouteservice extends ConfigurableProviderUsingHandlerWithAdapterBase {}
