<?php

namespace Drupal\office365_calendar\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class SettingsForm.
 *
 * @package Drupal\office365_calendar\Form
 */
class SettingsForm extends ConfigFormBase {


  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'office365_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'office365_calendar.settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('office365_calendar.settings');
    $form['client_id'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Client ID'),
      '#description' => $this->t('Client ID obtained from registration of this web app.'),
      '#maxlength' => 36,
      '#size' => 64,
      '#default_value' => $config->get('client_id'),
    );
        $form['client_secret'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Client Secret'),
      '#description' => $this->t('Client secret obtained from registration of this web app.'),
      '#maxlength' => 23,
      '#size' => 64,
      '#default_value' => $config->get('client_secret') == NULL ? '' : 'hidden',
    );
    $form['redirect_URI'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Redirect URI'),
      '#description' => $this->t('Redirect URI specified in the request determines how the authorization code is returned to your application.'),
      '#maxlength' => 256,
      '#size' => 64,
      '#default_value' => $config->get('redirect_URI'),
    );

    $form['submit'] = array(
        '#type' => 'submit',
        '#value' => t('Submit'),
    );

    return $form;
  }

  /**
    * {@inheritdoc}
    */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('office365_calendar.settings')
      ->set('client_id', $form_state->getValue('client_id'))
      ->set('client_secret', $form_state->getValue('client_secret'))
      ->set('redirect_URI', $form_state->getValue('redirect_URI'))
      ->save();

    drupal_set_message('Settings saved');
    }

}
