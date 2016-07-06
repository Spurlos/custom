<?php

/**
 * @file
 * Contains \Drupal\like_flag\Event\FlagEvent.
 */

namespace Drupal\like_flag\Event;

use Drupal\node\Entity\Node;
use Drupal\user\Entity\User;
use Symfony\Component\EventDispatcher\Event;

class FlagEvent extends Event {

  protected $nid;

  protected $uid;

  public function __construct($nid, $uid) {
    $this->nid = $nid;
    $this->uid = $uid;
  }
  
  public function getFlaggedNode(){
    return Node::load($this->nid);
  }
  
  public function getFlaggingUser(){
    return User::load($this->uid);
  }
}