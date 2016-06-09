<?php
/**
 * Provides My Site Configuration Block
 *
 * @Block(
 *   id = "mysite_config_block",
 *   admin_label = @Translation("My Site Configuration block"),
 * )
 */

namespace Drupal\mysite_config\Plugin\Block;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Session\AccountInterface;

class ConfigBlock extends BlockBase {

  public function build() {
    return \Drupal::formBuilder()
      ->getForm('Drupal\mysite_config\Form\ConfigForm');
  }

  public function blockAccess(AccountInterface $account) {
    $parent_access = parent::blockAccess($account);
    if ($parent_access->isAllowed() && $account->hasPermission('access administration pages')) {
      return AccessResult::allowed();
    }

    return AccessResult::forbidden();
  }
}
