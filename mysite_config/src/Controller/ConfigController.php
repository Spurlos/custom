<?php
/**
 * @file
 * Contains \Drupal\mysite_config\Controller\ConfigController.
 */

namespace Drupal\mysite_config\Controller;

use Drupal\Core\Controller\ControllerBase;

class ConfigController extends ControllerBase {
    public function main() {
        return array(
            '#type' => 'markup',
            '#markup' => $this->t('Hello, World!'),
        );
    }
}