<?php

/**
 * @file
 * Contains like_flag\like_flag.views.inc..
 * Provide a custom views field data that isn't tied to any other module. */

use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\Sql\SqlContentEntityStorage;
use Drupal\Core\Render\Markup;
use Drupal\field\FieldConfigInterface;
use Drupal\field\FieldStorageConfigInterface;
use Drupal\system\ActionConfigEntityInterface;

/**
 * Implements hook_views_data().
 */
function like_flag_views_data() {
  $data['flaggings']['table']['group'] = t('Like Counts');
  $data['flaggings']['table']['join'] = array(
    'node_field_data' => array(
//     'type' => 'LEFT',
      'left_field' => 'nid',
      'field' => 'nid',
    ),
//    '#global' => array(),
  );

  $data['flaggings']['like_flaggings'] = array(
    'title' => t('Flagging count field'),
    'help' => t('Displays flagging count for particular node.'),
    'field' => array(
      'id' => 'numeric',
    ),
    'sort' => array(
      'id' => 'numeric',
    ),
    'filter' => array(
      'id' => 'numeric',
    ),
  );

  return $data;
}
