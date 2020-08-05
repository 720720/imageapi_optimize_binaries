<?php

namespace Drupal\imageapi_optimize_binaries\Plugin\ImageAPIOptimizeProcessor;

use Drupal\Core\Form\FormStateInterface;
use Drupal\imageapi_optimize_binaries\ImageAPIOptimizeProcessorBinaryBase;

/**
 * Uses the GifSicle binary to optimize images.
 *
 * @ImageAPIOptimizeProcessor(
 *   id = "gifsicle",
 *   label = @Translation("GifSicle"),
 *   description = @Translation("Uses the GifSicle binary to optimize images.")
 * )
 */
class GifSicle extends ImageAPIOptimizeProcessorBinaryBase {

  /**
   * {@inheritdoc}
   */
  protected function executableName() {
    return 'gifsicle';
  }

  public function applyToImage($image_uri) {
    if ($cmd = $this->getFullPathToBinary()) {

      if ($this->getMimeType($image_uri) == 'image/gif') {
        $options = array();

        $options[] = '-O' . $this->configuration['level'];

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
      'level' => 3,
    ];
  }

  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildConfigurationForm($form, $form_state);

    $form['level'] = array(
      '#title' => $this->t('Optimization level'),
      '#type' => 'select',
      '#options' => range(1, 3),
      '#default_value' => $this->configuration['level'],
    );

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
    parent::submitConfigurationForm($form, $form_state);

    $this->configuration['level'] = $form_state->getValue('level');
  }
}
