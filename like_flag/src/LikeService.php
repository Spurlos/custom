<?php

namespace Drupal\like_flag;

use Drupal\Core\Database\Driver\mysql\Connection;

/**
 * Class LikeService.
 *
 * @package Drupal\like_flag
 */
class LikeService implements LikeServiceInterface {

  /**
   * Drupal\Core\Database\Driver\mysql\Connection definition.
   *
   * @var \Drupal\Core\Database\Driver\mysql\Connection
   */
  protected $database;
  /**
   * Constructor.
   */
  public function __construct(Connection $database) {
    $this->database = $database;
  }

  public function isFlagged($node, $user) {
    // TODO: Implement isFlagged() method.
  }

  public function addFlagging($node, $user) {
    // TODO: Implement addFlagging() method.
  }

  public function existFlaggingCount($node) {
    // TODO: Implement existFlaggingCount() method.
  }

  public function updateFlaggingCount($node, $action) {
    // TODO: Implement updateFlaggingCount() method.
  }

  public function addFlaggingCount($node) {
    // TODO: Implement addFlaggingCount() method.
  }

  public function removeFlagging($node, $user) {
    // TODO: Implement removeFlagging() method.
  }
}
