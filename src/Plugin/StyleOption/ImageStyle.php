<?php

declare(strict_types=1);

namespace Drupal\fsu_strata_mercury_editor\Plugin\StyleOption;

use Drupal\Core\Url;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\file\Entity\File;
use Drupal\Component\Utility\Bytes;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Component\Utility\Environment;
use Drupal\Core\Ajax\AjaxHelperTrait;
use Drupal\style_options\StyleOptionStyleTrait;
use Drupal\style_options\Plugin\StyleOptionPluginBase;

/**
 * Define the image attribute option plugin.
 *
 * @StyleOption(
 *   id = "image_style",
 *   label = @Translation("Image Style")
 * )
 */
class ImageStyle extends StyleOptionPluginBase {

  use AjaxHelperTrait;
  use StyleOptionStyleTrait;

  protected function getSource(array $build, string $source) {
    $parts = explode('.', $source);

    if (empty($build[$parts[0]]['#items']) || empty($build[$parts[0]]['#items']->entity)) {
      return;
    }

    $entity = $build[$parts[0]]['#items']->entity;

    if (empty($entity->{$parts[1]})) {
      return;
    }

    return $entity->{$parts[1]}->first();
  }

  /**
   * Builds the configuration form.
   *
   * @param array $form
   *   The form array.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The Drupal FormState class.
   *
   * @return array
   *   The complete form render array.
   */
  public function buildConfigurationForm(
    array $form,
    FormStateInterface $form_state): array {

    $styles = $this->entityTypeManager->getStorage('image_style')->loadMultiple();
    if (empty($styles)) {
      return $form;
    }

    if ($this->hasConfiguration('options')) {
      $styles = array_filter($styles, function ($style) {
        return in_array($style, $this->getConfiguration('options'));
      }, ARRAY_FILTER_USE_KEY);
    }
    $options = array_map(
      function ($style) {
        return $style->label();
      },
      $styles
    );

    $form['image_style'] = [
      '#type' => 'select',
      '#title' => $this->getLabel(),
      '#default_value' => $this->getValue('image_style') ? $this->getValue('image_style') : $this->getDefaultValue(),
      '#wrapper_attributes' => [
        'class' => [$this->getConfiguration('image_style') ?? ''],
      ],
      '#description' => $this->getConfiguration('description'),
      '#options' => $options,
    ];

    return $form;
  }

  /**
   * {@inheritDoc}
   */
  public function build(array $build, $value = '') {
    $image = $this->getSource($build, $this->getConfiguration('source'));
    if (empty($image)) {
      return $build;
    }
    $value = $this->getValue('image_style') ? $this->getValue('image_style') : $this->getDefaultValue();
    $style = $this->entityTypeManager->getStorage('image_style')->load($value);
    if (!$style) {
      return $build;
    }
    $uri = $image->entity->getFileUri();
    $properties = $image->getValue();

    $build[$this->getOptionId()] = [
      '#theme' => 'image_style',
      '#style_name' => $value,
      '#uri' => $uri,
      '#width' => $properties['width'],
      '#height' => $properties['height'],
    ];
    return $build;
  }

}
