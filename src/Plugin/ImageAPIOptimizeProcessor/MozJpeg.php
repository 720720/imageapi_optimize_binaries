<?php

namespace Drupal\imageapi_optimize_binaries\Plugin\ImageAPIOptimizeProcessor;

use Drupal\Core\Form\FormStateInterface;
use Drupal\imageapi_optimize_binaries\ImageAPIOptimizeProcessorBinaryBase;

/**
 * Uses the MozJpeg binary to optimize images.
 *
 * @ImageAPIOptimizeProcessor(
 *   id = "mozjpeg",
 *   label = @Translation("MozJpeg"),
 *   description = @Translation("Uses the MozJpeg binary to optimize images.")
 * )
 */
class MozJpeg extends ImageAPIOptimizeProcessorBinaryBase {

  /**
   * {@inheritdoc}
   */
  protected function executableName() {
    return 'mozjpeg';
  }

  public function applyToImage($image_uri) {
    if ($cmd = $this->getFullPathToBinary()) {

      if ($this->getMimeType($image_uri) == 'image/jpeg') {
        $options = array();

        if ($this->configuration['progressive']) {
          $options[] = '-progressive';
        }

        if (is_numeric($this->configuration['quality'])) {
          $options[] = '-quality ' . escapeshellarg($this->configuration['quality']);
        }

        $dst = $this->sanitizeFilename($image_uri);

        $arguments = array(
          $dst,
        );

        $option_string = implode(' ', $options);
        $argument_string = implode(' ', array_map('escapeshellarg', $arguments));
        return $this->saveCommandStdoutToFile(escapeshellarg($cmd) . ' ' . $option_string . ' ' . $argument_string, $dst);
      }
    }

    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return parent::defaultConfiguration() + [
      'progressive' => TRUE,
      'quality' => 90,
    ];
  }

  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildConfigurationForm($form, $form_state);

    $form['progressive'] = array(
      '#title' => $this->t('Progressive'),
      '#type' => 'checkbox',
      '#default_value' => $this->configuration['progressive'],
    );

    $form['quality'] = array(
      '#title' => $this->t('Quality'),
      '#type' => 'number',
      '#min' => 0,
      '#max' => 100,
      '#description' => $this->t('Compression quality (0..100; 5-95 is most useful range, default is 75)'),
      '#default_value' => $this->configuration['quality'],
    );

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
    parent::submitConfigurationForm($form, $form_state);

    $this->configuration['progressive'] = $form_state->getValue('progressive');
    $this->configuration['quality'] = $form_state->getValue('quality');
  }
}
