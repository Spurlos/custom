<?php

namespace Drupal\like_flag\Form;

use Drupal\Component\Utility\Html;
use Drupal\Core\Ajax\InsertCommand;
use Drupal\Core\Ajax\ReplaceCommand;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\HtmlCommand;
use Drupal\like_flag\LikeService;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class LikeFlagForm.
 *
 * @package Drupal\like_flag\Form
 */
class LikeFlagForm extends FormBase {

  protected $likeService;

  /**
   * Constructs the NodeTypeForm object.
   *
   * @param \Drupal\Core\Entity\EntityManagerInterface $entity_manager
   *   The entity manager.
   */
  public function __construct(LikeService $likeService) {
    $this->likeService = $likeService;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('like_flag')
    );
  }

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
    $like_flag = \Drupal::service('like_flag');

    if ($like_flag->isFlagged($nid, $uid)){
      $like_button_value = 'Unlike';
    }
    else {
      $like_button_value = 'Like';
    }

    $likes_count_value = $like_flag->getFlaggingCount($nid);

    $like_id = Html::getUniqueId('like');

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

    $like_flag = \Drupal::service('like_flag');
    if (!$like_flag->isFlagged($nid, $uid)){
      $like_flag->addFlagging($nid, $uid);
      if ($like_flag->existFlaggingCount($nid)){
        $like_flag->updateFlaggingCount($nid, '+');
      }
      else {
        $like_flag->addFlaggingCount ($nid);
      }
      
      $form['like']['button']['#value'] = 'Unlike';
      $form['like']['count']['#markup'] += 1;
    }
    else{
      $like_flag->removeFlagging($nid, $uid);
      $like_flag->updateFlaggingCount($nid, '-');

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
