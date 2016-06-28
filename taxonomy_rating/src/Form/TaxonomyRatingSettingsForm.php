<?php

namespace Drupal\taxonomy_rating\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class TaxonomyRatingSettingsForm.
 *
 * @package Drupal\taxonomy_rating\Form
 */
class TaxonomyRatingSettingsForm extends ConfigFormBase {


  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'taxonomy_rating_settings_form';
  }

  protected function getEditableConfigNames() {
    return [
      'taxonomy_rating.settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $fields = [];
//    $test = \Drupal::entityTypeManager()->getDefinition('taxonomy_vocabulary');
    $entity_type_id = 'taxonomy_term';
    $bundle = 'genres';
    foreach (\Drupal::entityManager()->getFieldDefinitions($entity_type_id, $bundle) as $field_name => $field_definition) {
      if (!empty($field_definition->getTargetBundle())) {
        $fields[$field_name] = $field_definition->getLabel();
      }
    }

    $config = $this->config('taxonomy_rating.settings');
    $form['book_weight'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Book weight'),
      '#description' => $this->t('The weight for each book in genre rating calculation'),
      '#maxlength' => 64,
      '#size' => 4,
      '#default_value' => $config->get('book_weight'),
    );
    $form['author_weight'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Author weight'),
      '#description' => $this->t('The weight for each author in genre rating calculation'),
      '#maxlength' => 64,
      '#size' => 4,
      '#default_value' => $config->get('author_weight'),
    );
    $form['calculation_method'] = [
      '#type' => 'radios',
      '#title' => $this->t('Calculate genre rating by'),
      '#options' => [
        'onEvent' => $this->t('Event subscriber'),
        'onCron' => $this->t('Cron task'),
      ],
      '#default_value' => $config->get('calculation_method'),
    ];
    $form['rating_storage_fieldname'] = array(
      '#type' => 'select',
      '#title' => $this->t('Field for storing genre rating'),
      '#options' => $fields,
      '#size' => 1,
      '#default_value' => $config->get('rating_storage_fieldname'),
    );
    $form['actions'] = array('#type' => 'actions');
    $form['actions']['submit'] = array(
      '#type' => 'submit',
      '#value' => $this->t('Save'),
      '#name' => 'submit',
    );
    $form['actions']['rebuild'] = array(
      '#type' => 'submit',
      '#value' => $this->t('Rebuild rating cache'),
      '#name' => 'rebuild',
    );

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $triggering_element = $form_state->getTriggeringElement();

    if ($triggering_element['#name']=='submit') {
      $this->config('taxonomy_rating.settings')
        ->set('book_weight', $form_state->getValue('book_weight'))
        ->set('author_weight', $form_state->getValue('author_weight'))
        ->set('calculation_method', $form_state->getValue('calculation_method'))
        ->set('rating_storage_fieldname', $form_state->getValue('rating_storage_fieldname'))
        ->save();

      drupal_set_message('Settings saved');
    }

    if ($triggering_element['#name']=='rebuild'){
      $config = $this->config('taxonomy_rating.settings');
      $rating_storage_fieldname = $config->get('rating_storage_fieldname');
      $query = \Drupal::entityQuery('taxonomy_term')
        ->condition('vid', 'genres');
      $genre_tids = $query->execute();
      $term_storage = \Drupal::entityManager()->getStorage('taxonomy_term');
      $terms = $term_storage->loadMultiple(array_values($genre_tids));
      foreach ($genre_tids as $genre_tid){
        $terms[$genre_tid]->$rating_storage_fieldname->value = \Drupal::service('taxonomy_rating')->calculate($genre_tid);
        $terms[$genre_tid]->save();
      }

      drupal_set_message('Cache rebuilt');
    }
  }

}
