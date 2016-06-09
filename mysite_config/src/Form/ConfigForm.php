<?php

/**
 * @file
 * Contains \Drupal\mysite_config\Form\ConfigForm.
 */

namespace Drupal\mysite_config\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

class ConfigForm extends FormBase {
   
    public function getFormId() {
        return 'mysite_config_form';
    }
    
    public function buildForm(array $form, FormStateInterface $form_state) {
        $form['site_name'] = array(
            '#type' => 'textfield',
            '#default_value' => \Drupal::config('system.site')->get('name'),
            '#title' => t('Site name'),
        );
        $form['maintenance_mode'] = array(
            '#type' => 'checkbox',
            '#default_value' => \Drupal::state()->get('system.maintenance_mode'),
            '#title' => t('Maintenance mode'),
        );

        $form['submit'] = array(
            '#type' => 'submit',
            '#value' => t('Save'),
            '#button_type' => 'primary',
        );
        
        return $form;
    }

    public function validateForm(array &$form, FormStateInterface $form_state) {
        if (strlen($form_state->getValue('site_name')) < 6) {
            $form_state->setErrorByName('site_name');
            drupal_set_message(t('The site name is too short. Please enter a longer site name.'), 'error');
        }
        if (strpos($form_state->getValue('site_name'), ' ') !== false) {
            $form_state->setErrorByName('site_name');
            drupal_set_message(t('The site name should not be more than one word.'), 'error');
        }

    }

    public function submitForm(array &$form, FormStateInterface $form_state) {
        \Drupal::configFactory()->getEditable('system.site')->set('name', $form_state->getValue('site_name'))->save();;
        \Drupal::state()->set('system.maintenance_mode', $form_state->getValue('maintenance_mode'));
    }
}