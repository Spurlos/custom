<?php

namespace Drupal\like_flag\Event;

/**
 * Contains all events thrown in the Like Flag module.
 */
final class FlagEvents {

  /**
   * Event ID for when a node is flagged.
   *
   * @Event
   *
   * @var string
   */
  const NODE_FLAGGED = 'like_flag.node_flagged';

  /**
   * Event ID for when a previously flagged node is unflagged.
   *
   * @Event
   *
   * @var string
   */
  const NODE_UNFLAGGED = 'like_flag.node_unflagged';
}