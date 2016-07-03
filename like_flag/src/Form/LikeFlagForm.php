<?php

namespace Drupal\like_flag\Form;

use Drupal\Component\Utility\Html;
use Drupal\Core\Ajax\InsertCommand;
use Drupal\Core\Ajax\ReplaceCommand;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\HtmlCommand;

/**
 * Class LikeFlagForm.
 *
 * @package Drupal\like_flag\Form
 */
class LikeFlagForm extends FormBase {


  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'like_flag_form';
  }


  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $args = $form_state->getBuildInfo()['args'];
    $nid = reset($args);
    dpm('buildForm nid:'.$nid);
    $user = \Drupal::currentUser();
    $uid = $user->id();

    $query = db_select('like_flags')
      ->condition('nid', $nid, '=')
      ->condition('uid', $uid, '=')
      ->fields('like_flags')
      ->execute();
    $isFlagged = $query->fetchField();
    if ($isFlagged){
      $like_button_value = 'Unlike';
    }
    else {
      $like_button_value = 'Like';
    }

    $query = db_select('flaggings')
      ->condition('nid', $nid, '=')
      ->fields('flaggings')
      ->execute();
    $likes_count_value = $query->fetchField(1);

    $like_id = Html::getUniqueId('like');
    $like_button_id = Html::getUniqueId('like-button');
    $likes_count_id = Html::getUniqueId('like-count');

    $form['like'] = [
      '#id' => $like_id,
      '#type' => 'container',
    ];
    $form['like']['button'] = [
      '#type' => 'submit',
      '#value' => $this->t($like_button_value),
      '#ajax' => [
        'callback' => '::ajaxCallback',
        'event' => 'click',
        'wrapper' => $like_id,
      ],
    ];
    $form['like']['count'] = [
      '#markup' => $likes_count_value,
    ];

    return $form;
  }
  
  public function ajaxCallback($form, FormStateInterface $form_state) {
    $args = $form_state->getBuildInfo()['args'];
    $nid = reset($args);
    $user = \Drupal::currentUser();
    $uid = $user->id();

    //service function candidate: isFlagged($node, $user)
    $query = db_select('like_flags')
      ->condition('nid', $nid, '=')
      ->condition('uid', $uid, '=')
      ->fields('like_flags')
      ->execute();
    $isFlagged = $query->fetchField();
    if (!$isFlagged){
      //service function candidate: addFlagging($node, $user)
      $query = db_insert('like_flags')
        ->fields(array(
          'nid' => $nid,
          'uid' => $uid,
        ));
      $query->execute();

      //service function candidate: existFlaggingCount($node)
      $query = db_select('flaggings')
        ->condition('nid', $nid, '=')
        ->fields('flaggings')
        ->execute();
      $existFlagging = $query->fetchField();

      if ($existFlagging){
        //service function candidate: updateFlaggingCount($node, $action)
        $query = db_update('flaggings')
          ->condition('nid', $nid, '=')
          ->expression('like_flaggings', 'like_flaggings + 1');
        $query->execute();
      }
      else {
        //service function candidate: addFlaggingCount ($node)
        $query = db_insert('flaggings')
          ->fields(array(
            'nid' => $nid,
            'like_flaggings' => 1,
          ));
        $query->execute();
      }
      
      $form['like']['button']['#value'] = 'Unlike';
      $form['like']['count']['#markup'] += 1;
    }
    else{
      //service function candidate: removeFlagging($node, $user)
      $query = db_delete('like_flags')
        ->condition('nid', $nid, '=')
        ->condition('uid', $uid, '=');
      $query->execute();

      //service function candidate: updateFlaggingCount($node, $action)
      $query = db_update('flaggings')
        ->condition('nid', $nid, '=')
        ->expression('like_flaggings', 'like_flaggings - 1');
      $query->execute();

      $form['like']['button']['#value'] = 'Like';
      $form['like']['count']['#markup'] -= 1;
    }
//    $response = new AjaxResponse();
//    $response->addCommand(new InsertCommand(NULL, $form['like']));
    return $form['like'];
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

  }

}
