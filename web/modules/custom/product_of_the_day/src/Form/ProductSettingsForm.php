<?php 
namespace Drupal\product_of_the_day\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

class ProductSettingsForm extends ConfigFormBase {

  public function getFormId() {
    return 'product_of_the_day_settings_form';
  }

  protected function getEditableConfigNames() {
    return ['product_of_the_day.settings'];
  }

  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('product_of_the_day.settings');

    $form['block_title'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Block title'),
      '#default_value' => $config->get('block_title') ?? 'Product of the Day',
    ];

    $form['admin_email'] = [
      '#type' => 'email',
      '#title' => $this->t('Admin email for reports'),
      '#default_value' => $config->get('admin_email') ?? '',
    ];

    return parent::buildForm($form, $form_state);
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('product_of_the_day.settings')
      ->set('block_title', $form_state->getValue('block_title'))
      ->set('admin_email', $form_state->getValue('admin_email'))
      ->save();

    parent::submitForm($form, $form_state);
  }
}
