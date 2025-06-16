<?php

namespace Drupal\product_of_the_day;

use Drupal\Core\Entity\Sql\SqlContentEntityStorage;

/**
 * Storage handler for Product entity.
 */
class ProductStorage extends SqlContentEntityStorage {
    /**
     * {@inheritdoc}
     */
    public function loadByProperties(array $values) {
        // Custom logic to load products by properties.
        $query = $this->getQuery();
        foreach ($values as $field => $value) {
        $query->condition($field, $value);
        }
        return $this->loadMultiple($query->execute());
    }
    
    /**
     * {@inheritdoc}
     */
    public function save($entity) {
        // Custom logic before saving the product.
        if ($entity->isNew()) {
            // Perform actions for new products, e.g., setting default values.
            if (!$entity->hasField('flagged')) {
                $entity->set('flagged', FALSE);
            }
        } else {
            // Perform actions for existing products, e.g., logging changes.
            \Drupal::logger('product_of_the_day')->info('Product with ID @id updated.', ['@id' => $entity->id()]);
        }
        return parent::save($entity);
    }
}
