<?php

namespace Drupal\geocoder_address\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\geocoder_field\Plugin\Field\FieldFormatter\GeocodeFormatter;
use Geocoder\Model\AddressCollection;
use Drupal\Component\Plugin\Exception\PluginException;

/**
 * Plugin implementation of the Geocode formatter.
 *
 * @FieldFormatter(
 *   id = "geocoder_address",
 *   label = @Translation("Geocode address"),
 *   field_types = {
 *     "address",
 *   }
 * )
 */
class AddressGeocodeFormatter extends GeocodeFormatter {

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];
    try {
      $dumper = $this->dumperPluginManager->createInstance($this->getSetting('dumper'));
    }
    catch (PluginException $e) {
      $this->loggerFactory->get('geocoder')->error('No Dumper has been set');
    }
    $provider_plugins = $this->getEnabledProviderPlugins();
    $geocoder_plugins_options = (array) $this->config->get('plugins_options');

    foreach ($items as $delta => $item) {
      $value = $item->getValue();
      $address = [];

      $address[] = !empty($value['address_line1']) ? $value['address_line1'] : NULL;
      $address[] = !empty($value['address_line2']) ? $value['address_line2'] : NULL;
      $address[] = !empty($value['postal_code']) ? $value['postal_code'] : NULL;
      $address[] = !empty($value['locality']) ? $value['locality'] : NULL;
      $address[] = !empty($value['country']) ? $value['country'] : NULL;

      if (isset($dumper) && $address_collection = $this->geocoder->geocode(implode(' ', array_filter($address)), array_keys($provider_plugins), $geocoder_plugins_options)) {
        $elements[$delta] = [
          '#markup' => $address_collection instanceof AddressCollection && !$address_collection->isEmpty() ? $dumper->dump($address_collection->first()) : "",
        ];
      }
    }

    return $elements;
  }

}
