<?php

namespace Drupal\like_flag;

use Drupal\Core\Database\Driver\mysql\Connection;
use Drupal\like_flag\Event\FlagEvent;
use Drupal\like_flag\Event\FlagEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

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
   * Symfony\Component\EventDispatcher\EventDispatcherInterface definition.
   *
   * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
   */
  protected $dispatcher;
  /**
   * Constructor.
   */
  public function __construct(Connection $database, EventDispatcherInterface $dispatcher) {
    $this->database = $database;
    $this->dispatcher = $dispatcher;
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
    
    $event = new FlagEvent($nid, $uid);
    $this->dispatcher->dispatch(FlagEvents::NODE_FLAGGED, $event);
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

    $event = new FlagEvent($nid, $uid);
    $this->dispatcher->dispatch(FlagEvents::NODE_UNFLAGGED, $event);
  }
}
