<?php

namespace Drupal\cat_widget\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a cat breed search block.
 *
 * @Block(
 *   id = "cat_breed_search",
 *   admin_label = @Translation("Cat breed search"),
 * )
 */
final class CatBreedSearchBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build(): array {
    $form = \Drupal::formBuilder()->getForm('Drupal\cat_widget\Form\CatSearchForm');
    return $form;
  }

}
