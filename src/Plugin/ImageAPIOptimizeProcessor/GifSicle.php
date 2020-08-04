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
          '-o ' . $dst,
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
      'level' => 3,
    ];
  }

  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildConfigurationForm($form, $form_state);

    $form['level'] = array(
      '#title' => $this->t('Optimization level'),
      '#type' => 'select',
      '#options' => array(
        1 => $this->t('Stores only the changed portion of each image. This is the default.'),
        2 => $this->t('Also uses transparency to shrink the file further.'),
        3 => $this->t('Try several optimization methods (usually slower, sometimes better results).'),
      ),
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
