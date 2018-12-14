<?php

namespace Drupal\geocoder_geofield\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\geocoder_field\Plugin\Field\FieldFormatter\FileGeocodeFormatter;

/**
 * Plugin implementation of the Geocode GPX" formatter for File fields.
 *
 * @FieldFormatter(
 *   id = "geocoder_geocode_formatter_gpxfile",
 *   label = @Translation("Geocode GPX data"),
 *   field_types = {
 *     "file",
 *   },
 *   description = "Extracts valid GPX data from the file content (if existing)"
 * )
 */
class GpxFileGeocodeFormatter extends FileGeocodeFormatter {

  /**
   * Unique Geocoder Plugin used by this formatter.
   *
   * @var string
   */
  protected $formatterPlugin = 'gpxfile';

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];
    try {
      /* @var \Drupal\geocoder\DumperInterface $dumper */
      $dumper = $this->dumperPluginManager->createInstance($this->getSetting('dumper'));
      /* @var \Drupal\geocoder_field\PreprocessorInterface $preprocessor */
      $preprocessor = $this->preprocessorManager->createInstance('file');
      $preprocessor->setField($items)->preprocess();
      foreach ($items as $delta => $item) {
        if ($address_collection = $this->geocoder->geocode($item->value, [$this->formatterPlugin])) {
          $elements[$delta] = [
            '#markup' => $dumper->dump($address_collection->first()),
          ];
        }
      }
    }
    catch (\Exception $e) {
      watchdog_exception('geocoder', $e);
    }

    return $elements;
  }

}
