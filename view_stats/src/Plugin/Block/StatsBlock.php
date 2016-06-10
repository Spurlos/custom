<?php

namespace Drupal\view_stats\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'StatsBlock' block.
 *
 * @Block(
 *  id = "stats_block",
 *  admin_label = @Translation("Statistics block"),
 * )
 */
class StatsBlock extends BlockBase {

  public function getCacheMaxAge(){
    return 0;
  }

  /**
   * {@inheritdoc}
   */
  public function build() {

    $node = \Drupal::routeMatch()->getParameter('node');
    if ($node) {
      $nid = $node->id();
    }
    else {
      return;
    }

    $user = \Drupal::currentUser();
    $uid = $user->id();
    $query = db_insert('node_stats')
      ->fields(array(
        'nid' => $nid,
        'uid' => $uid,
        'timestamp' => REQUEST_TIME,
      ))
      ->execute();

    $query = db_select('node_stats')
      ->condition('nid', $nid, '=')
      ->fields('node_stats')
      ->countQuery()
      ->execute();
    $total_views = $query->fetchField();

    $start_time = REQUEST_TIME-86400;
    $end_time = REQUEST_TIME;
    $query = db_select('node_stats')
      ->condition('nid', $nid, '=')
      ->condition('timestamp', array($start_time, $end_time), 'BETWEEN')
      ->fields('node_stats')
      ->countQuery()
      ->execute();
    $today_views = $query->fetchField();

    $query = db_select('node_stats')
      ->condition('nid', $nid, '=')
      ->orderBy('timestamp', 'DESC')
      ->range(0,1)
      ->fields('node_stats', array('uid','timestamp'))
      ->execute();
    $result = $query->fetchAssoc();
    $uid = $result['uid'];
    $date = format_date($result['timestamp']);

    $user = user_load($uid);
    $username = $user->getUsername();

    /*$user_query = db_select('users_field_data')
      ->fields('users_field_data', array('uid','name'));

    $last_username = $query->join($user_query, 'lastuser', 'node_stats.uid = myalias.uid');
    dpm($last_username);*/

    $output = array(
      'first_p' => array(
        '#type' => 'markup',
        '#markup' => $this->t('Number of views: :today today / :total total',
          [':today' => $today_views,
          ':total' => $total_views]
        ),
      ),
      'second_p' => array(
        '#type' => 'markup',
        '#markup' => $this->t('<p>Last viewed by: :user at :date',
          [':user' => $username,
          ':date' => $date]
        )
      ),

    );
    return $output;
  }

}
