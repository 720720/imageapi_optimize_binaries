<?php

namespace Drupal\imageapi_optimize_binaries\Plugin\ImageAPIOptimizeProcessor;

use Drupal\Core\Form\FormStateInterface;
use Drupal\imageapi_optimize_binaries\ImageAPIOptimizeProcessorBinaryBase;

/**
 * Uses the Guetzli binary to optimize images.
 *
 * @ImageAPIOptimizeProcessor(
 *   id = "guetzli",
 *   label = @Translation("Guetzli"),
 *   description = @Translation("Uses the Guetzli binary to optimize images.")
 * )
 */
class Guetzli extends ImageAPIOptimizeProcessorBinaryBase {

  /**
   * {@inheritdoc}
   */
  protected function executableName() {
    return 'guetzli';
  }

  public function applyToImage($image_uri) {
    if ($cmd = $this->getFullPathToBinary()) {

      if ($this->getMimeType($image_uri) == 'image/jpeg') {
        $options = array();

        if (is_numeric($this->configuration['quality'])) {
          $options[] = '--quality ' . escapeshellarg($this->configuration['quality']);
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
      'quality' => 95,
    ];
  }

  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildConfigurationForm($form, $form_state);

    $form['quality'] = array(
      '#title' => $this->t('Quality'),
      '#type' => 'number',
      '#min' => 84,
      '#max' => 100,
      '#description' => $this->t('Visual quality to aim for, expressed as a JPEG quality value.'),
      '#default_value' => $this->configuration['quality'],
    );

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
    parent::submitConfigurationForm($form, $form_state);

    $this->configuration['quality'] = $form_state->getValue('quality');
  }
}
