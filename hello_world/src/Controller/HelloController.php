<?php
/**
 * @file
 * Contains \Drupal\hello_world\Controller\HelloController.
 */

namespace Drupal\hello_world\Controller;

use Drupal\Core\Controller\ControllerBase;

class HelloController extends ControllerBase {
    public function content() {
        return array(
            '#type' => 'markup',
            '#markup' => $this->t('Hello, World!'),
        );
    }

    public function name($name) {
        return array(
            '#type' => 'markup',
            '#markup' => $this->t('Hello, :name', [':name' => $name]),
        );
    }
}