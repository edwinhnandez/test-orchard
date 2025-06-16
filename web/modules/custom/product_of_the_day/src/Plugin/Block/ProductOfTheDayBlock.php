<?php

namespace Drupal\product_of_the_day\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\product\Entity\Product;

/**
 * @Block(
 *   id = "product_of_the_day_block",
 *   admin_label = @Translation("Product of the Day")
 * )
 */
class ProductOfTheDayBlock extends BlockBase {
  public function build() {
    $storage = \Drupal::entityTypeManager()->getStorage('product');
    $query = $storage->getQuery()
      ->condition('flagged', 1)
      ->accessCheck(FALSE);

    $ids = $query->execute();

    if (empty($ids)) {
      return ['#markup' => 'No featured products found.'];
    }

    // Pick a random product ID.
    $random_id = array_rand($ids);
    $product = $storage->load($random_id);

    $image_url = '';
    if ($product->get('image')->entity) {
      $file = $product->get('image')->entity;
      $image_url = \Drupal::service('file_url_generator')->generateAbsoluteString($file->getFileUri());
    }

    return [
      '#theme' => 'product_of_the_day',
      '#title' => $product->label(),
      '#summary' => $product->get('summary')->value,
      '#image' => $image_url,
      '#cta_url' => Url::fromRoute('entity.product.canonical', ['product' => $product->id()])->toString(),
    ];
  }
}
