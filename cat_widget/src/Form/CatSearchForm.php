<?php

namespace Drupal\cat_widget\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

/**
 * Provides a Cat Widget form.
 */
final class CatSearchForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'cat_widget_cat_search';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {

    $client = \Drupal::service('cat_widget.cat_client');
    $breeds = $client->getBreeds();
    $options = [];
    foreach ($breeds as $breed) {
      $options[$breed['id']] = $breed['name'];
      $form_state->set($breed['id'], $breed);
    }

    $form['breed'] = [
      '#type' => 'select',
      '#title' => $this->t('Select cat breed'),
      '#options' => $options,
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Search'),
      '#ajax' => [
        'callback' => '::ajaxResponse',
        'wrapper' => 'cat-results',
        'method' => 'replace',
        'effect' => 'fade',
      ],
    ];

    $form['results'] = [
      '#type' => 'container',
      '#attributes' => ['id' => 'cat-results'],
    ];

    return $form;
  }

  /**
   * AJAX response callback for the form.
   */
  public function ajaxResponse(array &$form, FormStateInterface $form_state) {
    $output = [];
    // Get details from selected breed.
    $breed_id = $form_state->getValue('breed');
    $breed_details = $form_state->get($breed_id);
    $output = [
      '#type' => 'container',
      '#attributes' => ['id' => 'cat-results'],
      '#theme' => 'item_list',
      '#items' => [
        $this->t('Name: @name', ['@name' => $breed_details['name']]),
        $this->t('Temperament: @temp', ['@temp' => $breed_details['temperament']]),
        $this->t('Description: @desc', ['@desc' => $breed_details['description']]),
        $breed_details['wikipedia_url'] ? [
          '#type' => 'link',
          '#title' => $this->t('Wikipedia'),
          '#url' => Url::fromUri($breed_details['wikipedia_url']),
          '#attributes' => ['target' => '_blank'],
        ] : NULL,
      ],
    ];

    // Take 3 random images from the selected breed.
    $client = \Drupal::service('cat_widget.cat_client');
    $images = $client->randomByBreed($breed_id, 3);
    if (!empty($images)) {
      // Limit the results to the first 3 images. The limit parameter does not
      // work in the API.
      $images = array_slice($images, 0, 3);
      foreach ($images as $image) {
        $output['#items'][] = [
          '#theme' => 'image',
          '#uri' => $image['url'],
          '#alt' => $image['name'],
        ];
      }
    }

    return $form['results'] = $output;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

  }

}
