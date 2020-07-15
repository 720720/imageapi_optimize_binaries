<?php

namespace Drupal\imageapi_optimize_binaries\Plugin\ImageAPIOptimizeProcessor;

use Drupal\Core\Form\FormStateInterface;
use Drupal\imageapi_optimize_binaries\ImageAPIOptimizeProcessorBinaryBase;

/**
 * Uses the ZopfliPng binary to optimize images.
 *
 * @ImageAPIOptimizeProcessor(
 *   id = "zopflipng",
 *   label = @Translation("ZopfliPng"),
 *   description = @Translation("Uses the ZopfliPng binary to optimize images.")
 * )
 */
class ZopfliPng extends ImageAPIOptimizeProcessorBinaryBase {

  /**
   * {@inheritdoc}
   */
  protected function executableName() {
    return 'zopflipng';
  }

  public function applyToImage($image_uri) {
    if ($cmd = $this->getFullPathToBinary()) {

      if ($this->getMimeType($image_uri) == 'image/png') {
        $options = array(
          '-y',
        );

        if ($this->configuration['more']) {
          $options[] = '-m';
        }

        $dst = $this->sanitizeFilename($image_uri);

        $arguments = array(
          $dst,
          $dst,
        );

        return $this->execShellCommand($cmd, $options, $arguments);
      }
    }

    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return parent::defaultConfiguration() + [
      'more' => FALSE,
    ];
  }

  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildConfigurationForm($form, $form_state);

    $form['more'] = array(
      '#title' => $this->t('compress more: use more iterations (depending on file size)'),
      '#type' => 'checkbox',
      '#default_value' => $this->configuration['more'],
    );

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
    parent::submitConfigurationForm($form, $form_state);

    $this->configuration['more'] = $form_state->getValue('more');
  }
}
