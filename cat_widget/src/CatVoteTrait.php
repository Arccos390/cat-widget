<?php

namespace Drupal\cat_widget;

/**
 * Provides helper methods for cat votes.
 */
trait CatVoteTrait {

  /**
   * Generate the vote markup.
   *
   * @param int $vote_up
   *   The number of upvotes.
   * @param int $vote_down
   *   The number of downvotes.
   *
   * @return string
   *   The formatted markup.
   */
  public function getVoteMarkup($vote_up, $vote_down) {
    return $this->t('Likes: @up | Dislikes: @down', ['@up' => $vote_up, '@down' => $vote_down]);
  }

}
