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

  public function isFlagged($nid, $uid) {
    $query = $this->database->select('like_flags')
      ->condition('nid', $nid, '=')
      ->condition('uid', $uid, '=')
      ->fields('like_flags')
      ->execute();
    return $query->fetchField();
  }

  public function addFlagging($nid, $uid) {
    $query = $this->database->insert('like_flags')
      ->fields(array(
        'nid' => $nid,
        'uid' => $uid,
      ));
    $query->execute();
  }

  public function existFlaggingCount($nid) {
    $query = $this->database->select('flaggings')
      ->condition('nid', $nid, '=')
      ->fields('flaggings')
      ->execute();
    return $query->fetchField();
  }

  public function updateFlaggingCount($nid, $action) {
    $query = $this->database->update('flaggings')
      ->condition('nid', $nid, '=');
      if($action=='+'){
        $query->expression('like_flaggings', 'like_flaggings + 1');
      }
      elseif ($action=='-'){
        $query->expression('like_flaggings', 'like_flaggings - 1');
      }
    $query->execute();
  }

  public function getFlaggingCount($nid) {
    $query = $this->database->select('flaggings')
      ->condition('nid', $nid, '=')
      ->fields('flaggings')
      ->execute();
    return $query->fetchField(1);
  }

  public function addFlaggingCount($nid) {
    $query = $this->database->insert('flaggings')
      ->fields(array(
        'nid' => $nid,
        'like_flaggings' => 1,
      ));
    $query->execute();
  }

  public function removeFlagging($nid, $uid) {
    $query = $this->database->delete('like_flags')
      ->condition('nid', $nid, '=')
      ->condition('uid', $uid, '=');
    $query->execute();
  }
}
