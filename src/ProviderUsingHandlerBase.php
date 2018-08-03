<?php

namespace Drupal\geocoder;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException;
use Geocoder\StatefulGeocoder;

/**
 * Provides a base class for providers using handlers.
 */
abstract class ProviderUsingHandlerBase extends ProviderBase {

  /**
   * The language code.
   *
   * @var string
   */
  protected $langCode;

  /**
   * The provider handler.
   *
   * @var \Geocoder\Provider\Provider
   */
  protected $handler;

  /**
   * The V4 Stateful handler wrapper.
   *
   * @var \Geocoder\StatefulGeocoder
   */
  protected $handlerWrapper;

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, ConfigFactoryInterface $config_factory, CacheBackendInterface $cache_backend) {
    if (empty($plugin_definition['handler'])) {
      throw new InvalidPluginDefinitionException($plugin_id, "Plugin '$plugin_id' should define a handler.");
    }

    parent::__construct($configuration, $plugin_id, $plugin_definition, $config_factory, $cache_backend);
  }

  /**
   * {@inheritdoc}
   */
  protected function doGeocode($source) {
    return $this->getHandlerWrapper()->geocode($source);
  }

  /**
   * {@inheritdoc}
   */
  protected function doReverse($latitude, $longitude) {
    return $this->getHandlerWrapper()->reverse($latitude, $longitude);
  }

  /**
   * Returns the provider handler.
   *
   * @return \Geocoder\Provider\Provider
   *   The provider plugin.
   *
   * @throws \ReflectionException
   */
  protected function getHandler() {
    if (!isset($this->handler)) {
      $definition = $this->getPluginDefinition();
      $reflection_class = new \ReflectionClass($definition['handler']);
      $this->handler = $reflection_class->newInstanceArgs($this->getArguments());
    }

    return $this->handler;
  }

  /**
   * Sets the language code to use.
   *
   * @param string $langCode
   *   The language code to use.
   */
  public function setLangCode($langCode) {
    $this->langCode = $langCode;
  }

  /**
   * Gets the language code to use with the stateful wrapper.
   *
   * @return string
   *   The language code.  Defaults to current language.
   */
  protected function getLangCode() {
    if (!isset($this->langCode)) {
      // TODO: need to inject language manager to do this properly.
      $this->langCode = \Drupal::languageManager()->getCurrentLanguage()->getId();
    }

    return $this->langCode;
  }

  /**
   * Returns the V4 Stateful wrapper.
   *
   * @return \Geocoder\StatefulGeocoder
   *   The current handler wrapped in this class.
   *
   * @throws \ReflectionException
   */
  protected function getHandlerWrapper() {
    if (!isset($this->handlerWrapper)) {
      $this->handlerWrapper = new StatefulGeocoder($this->getHandler(), $this->getLangCode());
    }

    return $this->handlerWrapper;
  }

  /**
   * Builds a list of arguments to be used by the handler.
   *
   * @return array
   *   The list of arguments for handler instantiation.
   */
  protected function getArguments() {
    $arguments = [];

    foreach ($this->getPluginDefinition()['arguments'] as $key => $argument) {
      // No default value has been passed.
      if (is_string($key)) {
        $config_name = $key;
        $default_value = $argument;
      }
      else {
        $config_name = $argument;
        $default_value = NULL;
      }

      $arguments[] = isset($this->configuration[$config_name]) ?
        $this->configuration[$config_name] :
        $default_value;
    }

    return $arguments;
  }

}
