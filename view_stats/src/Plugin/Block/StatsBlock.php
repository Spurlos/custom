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
    $build = [];
    $build['stats_block']['#markup'] = 'Implement StatsBlock!!.';


    $node = \Drupal::routeMatch()->getParameter('node');
    if ($node) {
      $nid = $node->id();
    }
    //dpm('nid '.$nid);
    $user = \Drupal::currentUser();
    $uid = $user->id();
    //dpm('uid '.$uid);
    $query = db_insert('node_stats')
      ->fields(array(
        'nid' => $nid,
        'uid' => $uid,
        'timestamp' => REQUEST_TIME,
      ))
      ->execute();
    //dpm($query);
    dpm('nid '.$nid);
    $query = db_select('node_stats')
      ->condition('nid', $nid, '=')
      ->fields('node_stats')
      ->countQuery()
      ->execute();
    $total_views = $query->fetchField();
    dpm('Total views: '.$total_views);

    $start_time = strtotime('today');
    dpm($start_time);
    //$start_time = 1465563401;
    $end_time = REQUEST_TIME;
    $query = db_select('node_stats')
      ->condition('nid', $nid, '=')
      ->condition('timestamp', array($start_time, $end_time), 'BETWEEN')
      ->fields('node_stats')
      ->countQuery()
      ->execute();
    $today_views = $query->fetchField();
    dpm('Today views: '.$today_views);

    return $build;
  }

}
