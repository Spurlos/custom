<?php

namespace Drupal\nodes_editor\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

/**
 * Class NodeEdit.
 *
 * @package Drupal\nodes_editor\Form
 */
class NodeEdit extends FormBase {


  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'node_edit';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $query = \Drupal::entityQuery('node');//->condition('type', 'book');
    $nids = $query->execute();

    $node_storage = \Drupal::entityManager()->getStorage('node');
    $nodes = $node_storage->loadMultiple(array_values($nids));

    //dpm($nids);

    //dpm($nodes[1]->title->value);

    $options = array();
    foreach ($nids as $nid) {
      //dpm($nid);
      $options[$nid] = $nodes[$nid]->title->value;
    }

    //dpm($options);

    $form['node_select'] = array(
      '#type' => 'select',
      '#title' => $this->t('Select a node'),
      '#options' => $options,
      '#size' => 1,
    );
    $form['published_state'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('Published'),
    );
    $form['sticky_state'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('Sticky'),
    );
    $form['actions'] = array('#type' => 'actions');
    $form['actions']['submit'] = array(
      '#type' => 'submit',
      '#value' => t('Save'),
      '#name' => 'submit',
    );
    $form['actions']['delete'] = array(
      '#type' => 'submit',
      '#value' => t('Delete'),
      '#name' => 'delete',
    );

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    $triggering_element = $form_state->getTriggeringElement();

    if ($triggering_element['#name']=='submit') {

      $query = \Drupal::entityQuery('node');//->condition('type', 'book');
      $nids = $query->execute();

      $node_storage = \Drupal::entityManager()->getStorage('node');
      $nodes = $node_storage->loadMultiple(array_values($nids));

      $isChanged = FALSE;

      if ($nodes[$form_state->getValue('node_select')]->status->value != $form_state->getValue('published_state')) {
        $nodes[$form_state->getValue('node_select')]->status->value = $form_state->getValue('published_state');
        dpm('Published state has been changed');
        if (!$isChanged) {
          $isChanged = TRUE;
        }
      }
      if ($nodes[$form_state->getValue('node_select')]->sticky->value != $form_state->getValue('sticky_state')) {
        $nodes[$form_state->getValue('node_select')]->sticky->value = $form_state->getValue('sticky_state');
        dpm('Sticky state has been changed');
        if (!$isChanged) {
          $isChanged = TRUE;
        }
      }
      if ($isChanged) {
        $nodes[$form_state->getValue('node_select')]->save();
      }
      else {
        dpm('No changes have been made');
      }
      //dpm($nodes[$form_state->getValue('node_select')]);
    }

    if ($triggering_element['#name']=='delete') {
      $url = new Url('nodes_editor.delete', array(
        'nid' => $form_state->getValue('node_select'),
      ));
      $form_state->setRedirectUrl($url);
    }
  }
}
