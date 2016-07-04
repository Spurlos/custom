<?php

namespace Drupal\like_flag;

/**
 * Interface LikeServiceInterface.
 *
 * @package Drupal\like_flag
 */
interface LikeServiceInterface {

  /**
   * Checks if flagging already exists for selected user and node.
   *
   * @param integer $nid 
   *   The node id for which to check the flag existence.
   * @param integer $uid
   *   The user id for which to check the flag existence.
   * @return mixed 
   *   The value of node id if it exists, or NULL if the property does
   *   not exist.
   */
  public function isFlagged($nid, $uid);

  /**
   * Add flagging for selected user and node.
   *
   * @param integer $nid
   *   The node id for which to set the flagging.
   * @param integer $uid
   *   The user id for which to set the flagging.
   * @return mixed
   */
  public function addFlagging($nid, $uid);

  /**
   * Checks if flagging counter exists for selected node.
   *
   * @param integer $nid
   *   The node id for which to check the flagging counter.
   * @return mixed
   *   The value of node id if it exists, or NULL if the property does
   *   not exist.
   */
  public function existFlaggingCount($nid);

  /**
   * @param $nid
   * @param $action
   * @return mixed
   */
  public function updateFlaggingCount($nid, $action);

  /**
   * @param $nid
   * @return mixed
   */
  public function getFlaggingCount($nid);

  /**
   * @param $nid
   * @return mixed
   */
  public function addFlaggingCount($nid);

  /**
   * @param $nid
   * @param $uid
   * @return mixed
   */
  public function removeFlagging($nid, $uid);

}
