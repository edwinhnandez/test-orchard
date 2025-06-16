<?php

declare(strict_types=1);
namespace Drupal\product_of_the_day\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;

class ProductForm extends ContentEntityForm {

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $entity = $this->getEntity();
    $status = parent::save($form, $form_state);

    switch ($status) {
      case SAVED_NEW:
        $this->messenger()->addStatus($this->t('Product %label has been created.', ['%label' => $entity->label()]));
        break;

      default:
        $this->messenger()->addStatus($this->t('Product %label has been updated.', ['%label' => $entity->label()]));
    }

    $form_state->setRedirect('entity.product.canonical', ['product' => $entity->id()]);
  }
}