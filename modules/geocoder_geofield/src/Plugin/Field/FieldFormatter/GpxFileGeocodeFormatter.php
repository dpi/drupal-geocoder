<?php

namespace Drupal\geocoder_geofield\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\geocoder\DumperPluginManager;
use Drupal\geocoder\Geocoder;
use Drupal\geocoder\ProviderPluginManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Render\RendererInterface;
use Drupal\Core\Utility\LinkGeneratorInterface;
use Drupal\geocoder_field\PreprocessorPluginManager;
use Drupal\geocoder_field\Plugin\Field\FieldFormatter\FileGeocodeFormatter;
use Drupal\geofield\WktGenerator;

/**
 * Plugin implementation of the Geocode GPX" formatter for File fields.
 *
 * @FieldFormatter(
 *   id = "geocoder_geocode_formatter_gpxfile",
 *   label = @Translation("Geocode GPX data"),
 *   field_types = {
 *     "file",
 *   },
 *   description =
 *   "Renders valid GPX data from the file content in WKT Linestring format"
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
   * The WKT Generator Service.
   *
   * @var \Drupal\geofield\WktGenerator
   */
  protected $wktGenerator;

  /**
   * Constructs a GeocodeFormatterFile object.
   *
   * @param string $plugin_id
   *   The plugin_id for the formatter.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Field\FieldDefinitionInterface $field_definition
   *   The definition of the field to which the formatter is associated.
   * @param array $settings
   *   The formatter settings.
   * @param string $label
   *   The formatter label display setting.
   * @param string $view_mode
   *   The view mode.
   * @param array $third_party_settings
   *   Any third party settings.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   A config factory for retrieving required config objects.
   * @param \Drupal\geocoder\Geocoder $geocoder
   *   The gecoder service.
   * @param \Drupal\geocoder\ProviderPluginManager $provider_plugin_manager
   *   The provider plugin manager service.
   * @param \Drupal\geocoder\DumperPluginManager $dumper_plugin_manager
   *   The dumper plugin manager service.
   * @param \Drupal\Core\Render\RendererInterface $renderer
   *   The renderer.
   * @param \Drupal\Core\Utility\LinkGeneratorInterface $link_generator
   *   The Link Generator service.
   * @param \Drupal\geocoder_field\PreprocessorPluginManager $preprocessor_manager
   *   The Preprocessor Manager.
   * @param \Drupal\geofield\WktGenerator $wkt_generator
   *   The WKT Generator Service.
   */
  public function __construct(
    $plugin_id,
    $plugin_definition,
    FieldDefinitionInterface $field_definition,
    array $settings,
    $label,
    $view_mode,
    array $third_party_settings,
    ConfigFactoryInterface $config_factory,
    Geocoder $geocoder,
    ProviderPluginManager $provider_plugin_manager,
    DumperPluginManager $dumper_plugin_manager,
    RendererInterface $renderer,
    LinkGeneratorInterface $link_generator,
    PreprocessorPluginManager $preprocessor_manager,
    WktGenerator $wkt_generator
  ) {
    parent::__construct(
      $plugin_id,
      $plugin_definition,
      $field_definition,
      $settings,
      $label,
      $view_mode,
      $third_party_settings,
      $config_factory,
      $geocoder,
      $provider_plugin_manager,
      $dumper_plugin_manager,
      $renderer,
      $link_generator,
      $preprocessor_manager
    );
    $this->preprocessorManager = $preprocessor_manager;
    $this->wktGenerator = $wkt_generator;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $plugin_id,
      $plugin_definition,
      $configuration['field_definition'],
      $configuration['settings'],
      $configuration['label'],
      $configuration['view_mode'],
      $configuration['third_party_settings'],
      $container->get('config.factory'),
      $container->get('geocoder'),
      $container->get('plugin.manager.geocoder.provider'),
      $container->get('plugin.manager.geocoder.dumper'),
      $container->get('renderer'),
      $container->get('link_generator'),
      $container->get('plugin.manager.geocoder.preprocessor'),
      $container->get('geofield.wkt_generator')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $element['intro'] = [
      '#type' => 'html_tag',
      '#tag' => 'div',
      '#value' => $this->pluginDefinition['description'],
    ];
    $element += parent::settingsForm($form, $form_state);
    $element['plugins'] = [
      $this->formatterPlugin => [
        'checked' => [
          '#type' => 'value',
          '#value' => TRUE,
        ],
      ],
    ];
    $element['plugin_info'] = [
      '#type' => 'item',
      '#title' => 'Plugin',
      '#markup' => $this->formatterPlugin,
    ];

    $element['dumper']['#options'] = ['wkt' => 'WKT Linestring'];
    $element['dumper']['#description'] = $this->t('This settings cannot be changed');

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = [];
    $summary['intro'] = $this->pluginDefinition['description'];
    $summary['plugins'] = t('Geocoder plugin(s): @formatterPlugin', [
      '@formatterPlugin' => $this->formatterPlugin,
    ]);
    $summary['dumper'] = t('Output format: WKT Linestring');
    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];
    try {
      /* @var \Drupal\geocoder_field\PreprocessorInterface $preprocessor */
      $preprocessor = $this->preprocessorManager->createInstance('file');
      $preprocessor->setField($items)->preprocess();
      foreach ($items as $delta => $item) {
        if ($address_collection = $this->geocoder->geocode($item->value, [$this->formatterPlugin])) {
          $addresses = $address_collection->all();
          $points = [];
          /* @var \Geocoder\Model\Address $address */
          foreach ($addresses as $address) {
            $points[] = [$address->getCoordinates()->getLongitude(), $address->getCoordinates()->getLatitude()];
          }
          $elements[$delta] = [
            '#markup' => $this->wktGenerator->wktBuildLinestring($points),
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
