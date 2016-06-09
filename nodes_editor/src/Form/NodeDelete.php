<?php

namespace Drupal\nodes_editor\Form;

use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Symfony\Component\Validator\Constraints\Null;

/**
 * Class NodeDelete.
 *
 * @package Drupal\nodes_editor\Form
 */
class NodeDelete extends ConfirmFormBase {

  private $nodeid = array();

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'node_delete';
  }

  public function getQuestion() {
    return t('Are you sure you want to delete content?');
  }

  public function getCancelUrl() {
    return new Url('nodes_editor.form');
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $nid = NULL) {
    $this->nodeid[] = $nid;
    dpm('buildForm ');
    dpm($this->nodeid);
    $form = parent::buildForm($form, $form_state);

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    dpm('submitForm ');
    dpm($this->nodeid);
    $node_storage = \Drupal::entityManager()->getStorage('node');
    $node = $node_storage->loadMultiple($this->nodeid);
    //dpm($node);
    $node_storage->delete($node);

    $form_state->setRedirect('nodes_editor.form');
  }

}
