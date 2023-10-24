<?php

namespace Drupal\cat_widget\Plugin\Block;

use Drupal\cat_widget\CatVoteTrait;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a cat of the day block.
 *
 * @Block(
 *   id = "cat_of_the_day",
 *   admin_label = @Translation("Cat of the day"),
 * )
 */
final class CatOfTheDayBlock extends BlockBase implements ContainerFactoryPluginInterface {

  use CatVoteTrait;

  /**
   * The entity type manager service.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Constructor.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, EntityTypeManagerInterface $entityTypeManager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->entityTypeManager = $entityTypeManager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_type.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build(): array {
    $build = [];

    // Load the latest media entity of type 'cat_widget_image'.
    $media_storage = $this->entityTypeManager->getStorage('media');
    $query = $media_storage->getQuery()
      ->condition('bundle', 'cat_widget_image')
      ->sort('created', 'DESC')
      ->accessCheck(TRUE)
      ->range(0, 1);
    $media_ids = $query->execute();

    if (!empty($media_ids)) {
      $media_id = reset($media_ids);
      $latest_media = $media_storage->load(reset($media_ids));

      // Load the image field from the media entity.
      $image_field = $latest_media->get('field_media_image')->getValue();

      // Check if the image field is not empty.
      if (!empty($image_field)) {
        // Load the file entity.
        $file = \Drupal\file\Entity\File::load($image_field[0]['target_id']);

        // Get the URI of the image.
        $image_uri = $file->getFileUri();

        // Build the render array for the image.
        $build['cat_image'] = [
          '#theme' => 'image',
          '#uri' => $image_uri,
          '#alt' => $this->t('Random cat image'),
          '#title' => $this->t('Random cat image'),
        ];

        // Load current vote counts.
        $vote_up = $latest_media->get('field_vote_up')->value;
        $vote_down = $latest_media->get('field_vote_down')->value;

        // Display the vote counts.
        $build['votes'] = [
          '#prefix' => '<div id="vote-counts">',
          '#suffix' => '</div>',
          '#markup' => $this->getVoteMarkup($vote_up, $vote_down)
        ];

        // Add buttons to vote.
        $build['vote_up'] = [
          '#type' => 'link',
          '#title' => $this->t('Vote Up'),
          '#url' => Url::fromRoute('cat_widget.vote', ['media_id' => $media_id, 'vote_type' => 'up']),
          '#attributes' => [
            'class' => ['button', 'vote-up', 'use-ajax'],
            'id' => 'vote-up-button',
          ],
        ];
        $build['vote_down'] = [
          '#type' => 'link',
          '#title' => $this->t('Vote Down'),
          '#url' => Url::fromRoute('cat_widget.vote', ['media_id' => $media_id, 'vote_type' => 'down']),
          '#attributes' => [
            'class' => ['button', 'vote-down', 'use-ajax'],
            'id' => 'vote-down-button',
          ],
        ];

        // Attach the AJAX library.
        $build['#attached']['library'][] = 'core/drupal.ajax';
        // Cache block.
        $build['#cache'] = [
          'tags' => $latest_media->getCacheTags(),
        ];
      }
    }
    else {
      $build['#markup'] = t('No cat images found.');
    }
    return $build;
  }

}
