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
    $form['actions'] = array('#type' => 'actions');
    $form['actions']['submit'] = array(
      '#type' => 'submit',
      '#value' => t('Save'),
      '#name' => 'submit',
    );
    $form['actions']['rebuild'] = array(
      '#type' => 'submit',
      '#value' => t('Rebuild rating cache'),
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
        ->save();
      dpm('Settings saved');
    }

    if ($triggering_element['#name']=='rebuild'){
      $query = \Drupal::entityQuery('taxonomy_term')
        ->condition('vid', 'genres');
      $genre_tids = $query->execute();
      $term_storage = \Drupal::entityManager()->getStorage('taxonomy_term');
      $terms = $term_storage->loadMultiple(array_values($genre_tids));
      foreach ($genre_tids as $genre_tid){
        $terms[$genre_tid]->field_genre_rating->value = taxonomy_rating_calculation($genre_tid);
        $terms[$genre_tid]->save();
      }

      dpm('Cache rebuilt');
    }
  }

}
