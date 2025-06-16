<?php

namespace Drupal\product_of_the_day;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Url;

class ProductListBuilder extends EntityListBuilder {

  public function buildHeader(): array {
    return [
      'id' => $this->t('ID'),
      'name' => $this->t('Name'),
      'flagged' => $this->t('Product of the Day?'),
    ] + parent::buildHeader();
  }

  public function buildRow(EntityInterface $entity): array {
    return [
      'id' => $entity->id(),
      'name' => $entity->label(),
      'flagged' => $entity->get('flagged')->value ? $this->t('Yes') : $this->t('No'),
    ] + parent::buildRow($entity);
  }

  public function getFormId(): string {
    return 'product_of_the_day_list';
  }

  public function getOperations(EntityInterface $entity): array {
    $operations = parent::getOperations($entity);
    $operations['edit']['url'] = Url::fromRoute('entity.product.edit_form', ['product' => $entity->id()]);
    $operations['delete']['url'] = Url::fromRoute('entity.product.delete_form', ['product' => $entity->id()]);
    return $operations;
  }
}