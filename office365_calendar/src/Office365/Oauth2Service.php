<?php

namespace Drupal\office365_calendar\Office365;

use Drupal\Core\Config\ConfigFactory;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\user\UserData;
use League\OAuth2\Client\Token\AccessToken;
use Stevenmaguire\OAuth2\Client\Provider\Microsoft;

require_once __DIR__ . '/../../vendor/autoload.php';

/**
 * Class Office365Oauth2Service.
 *
 * @package Drupal\office365_calendar
 */
class Oauth2Service implements Oauth2ServiceInterface {

  /**
   * Drupal\Core\Config\ConfigFactory definition.
   *
   * @var \Drupal\Core\Config\ConfigFactory
   */
  protected $configFactory;
  /**
   * Drupal\user\UserData definition.
   *
   * @var \Drupal\user\UserData
   */
  protected $userData;

  protected $provider;

  /**
   * Constructor.
   */
  public function __construct(ConfigFactory $config_factory, UserData $user_data) {
    $this->configFactory = $config_factory;
    $this->userData = $user_data;

    $config = $this->configFactory->get('office365_calendar.settings');
    $this->provider = new Microsoft([
      'clientId' => $config->get('client_id'),
      'clientSecret' => $config->get('client_secret'),
      'redirectUri' => $config->get('redirect_URI'),
    ]);
  }

  public function authorize($uid) {
    if (!isset($_GET['code'])) {
      // If we don't have an authorization code then get one
      $authUrl = $this->provider->getAuthorizationUrl();
//      $request = new Request();
//      $request->query->get('code');
//      $request->setSession();
      $_SESSION['oauth2state'] = $this->provider->getState();
      header('Location: ' . $authUrl);
      exit;
// Check given state against previously stored one to mitigate CSRF attack
    }
    elseif (empty($_GET['state']) || ($_GET['state'] !== $_SESSION['oauth2state'])) {
      unset($_SESSION['oauth2state']);
      drupal_set_message('Invalid state. Please try again.');
      exit;
    }
    else {
      // Try to get an access token (using the authorization code grant)
      $token = $this->provider->getAccessToken('authorization_code', [
        'code' => $_GET['code']
      ]);
      $this->saveToken($uid, $token);
    }
  }

  // Use this to interact with an API on the users behalf
  public function getAccessToken($uid) {
    $token = $this->loadToken($uid);
    if (($token->getExpires() <= time() ? true : false)){
      $token = $this->refreshToken($token);
      //TODO: Implement notification for re-authentication if refresh token has expired.
      $this->saveToken($uid, $token);
    }
    return $token->getToken();
  }

  public function refreshToken(AccessToken $token) {
    return $token = $this->provider->getAccessToken('refresh_token', [
      'refresh_token' => $token->getRefreshToken()
    ]);
  }

  public function tokenExists($uid){
    return ($this->userData->get('office365_calendar', $uid, 'token') != NULL ? TRUE : FALSE);
  }

  public function saveToken($uid, AccessToken $token) {
    $this->userData->set('office365_calendar', $uid, 'token' , $token);
  }

  /**
   * @param $uid
   * @return \League\OAuth2\Client\Token\AccessToken
   */
  public function loadToken($uid) {
    return $this->userData->get('office365_calendar', $uid, 'token');
  }

  public function deleteToken($uid) {
    $this->userData->delete('office365_calendar', $uid, 'token');
  }

}
