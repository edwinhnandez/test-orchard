<?php
// File: src/Controller/ProductController.php

namespace Drupal\product_of_the_day\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\product_of_the_day\Entity\Product;

/**
 * Returns product content.
 */
class ProductController extends ControllerBase {

  /**
   * Displays a list of products flagged as "Product of the Day".
   */
  public function listFlagged() {
    $build = [
      '#theme' => 'item_list',
      '#title' => $this->t('Products of the Day'),
      '#items' => [],
    ];

    $ids = \Drupal::entityQuery('product')
      ->condition('flagged', TRUE)
      ->execute();

    $products = Product::loadMultiple($ids);

    foreach ($products as $product) {
      $build['#items'][] = [
        '#markup' => $product->label(),
      ];
    }

    return $build;
  }
  
  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static();
  }

}