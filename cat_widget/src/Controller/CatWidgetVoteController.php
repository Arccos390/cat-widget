<?php

namespace Drupal\cat_widget\Controller;

use Drupal\cat_widget\CatVoteTrait;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\ReplaceCommand;
use Drupal\Core\Controller\ControllerBase;

/**
 * Returns responses for Cat Widget routes.
 */
final class CatWidgetVoteController extends ControllerBase {

  use CatVoteTrait;

  /**
   * Builds the response.
   */
  public function vote($media_id, $vote_type) {
    $media = \Drupal::entityTypeManager()->getStorage('media')->load($media_id);

    if ($vote_type == 'up') {
      $current_value = $media->get('field_vote_up')->value;
      $media->set('field_vote_up', $current_value + 1);
    }
    elseif ($vote_type == 'down') {
      $current_value = $media->get('field_vote_down')->value;
      $media->set('field_vote_down', $current_value + 1);
    }
    $media->save();

    // Build the updated vote counts.
    $vote_up = $media->get('field_vote_up')->value;
    $vote_down = $media->get('field_vote_down')->value;
    $markup = $this->getVoteMarkup($vote_up, $vote_down);

    // Return an AJAX response to update the vote counts on the page.
    $response = new AjaxResponse();
    $response->addCommand(new ReplaceCommand('#vote-counts', "<div id='vote-counts'>{$markup}</div>"));

    return $response;
  }
}
