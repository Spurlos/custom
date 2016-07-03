<?php

namespace Drupal\like_flag;

/**
 * Interface LikeServiceInterface.
 *
 * @package Drupal\like_flag
 */
interface LikeServiceInterface {

  public function isFlagged($node, $user);

  public function addFlagging($node, $user);

  public function existFlaggingCount($node);

  public function updateFlaggingCount($node, $action);

  public function addFlaggingCount($node);

  public function removeFlagging($node, $user);

}
