<?php

namespace Drupal\taxonomy_rating\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Class RatingController.
 *
 * @package Drupal\taxonomy_rating\Controller
 */
class RatingController extends ControllerBase {

  /**
   * Hello.
   *
   * @return string
   *   Return Hello string.
   */
  public function content() {
    $query = \Drupal::entityQuery('node')->condition('type', 'book');
    $nids = $query->execute();


    return [
      '#type' => 'markup',
      '#markup' => $this->t('Implement method: hello')
    ];
  }

}
