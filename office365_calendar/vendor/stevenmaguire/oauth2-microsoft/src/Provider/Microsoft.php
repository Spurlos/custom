<?php namespace Stevenmaguire\OAuth2\Client\Provider;

use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Token\AccessToken;
use Psr\Http\Message\ResponseInterface;

class Microsoft extends AbstractProvider
{
    /**
     * Default scopes
     *
     * @var array
     */
  public $defaultScopes = [
    'https://outlook.office.com/calendars.readwrite',
    'offline_access'
  ];

    /**
     * Get authorization url to begin OAuth flow
     *
     * @return string
     */
    public function getBaseAuthorizationUrl()
    {
        return 'https://login.microsoftonline.com/common/oauth2/v2.0/authorize';
    }

    /**
     * Get access token url to retrieve token
     *
     * @return string
     */
    public function getBaseAccessTokenUrl(array $params)
    {
        return 'https://login.microsoftonline.com/common/oauth2/v2.0/token';
    }

    /**
     * Get default scopes
     *
     * @return array
     */
    protected function getDefaultScopes()
    {
        return $this->defaultScopes;
    }

  /**
   * {@inheritdoc}
   */
    protected function getScopeSeparator()
    {
        return ' ';
    }

    /**
     * Check a provider response for errors.
     *
     * @throws IdentityProviderException
     * @param  ResponseInterface $response
     * @return void
     */
    protected function checkResponse(ResponseInterface $response, $data)
    {
        if (isset($data['error'])) {
            throw new IdentityProviderException(
                (isset($data['error']['message']) ? $data['error']['message'] : $response->getReasonPhrase()),
                $response->getStatusCode(),
                $response
            );
        }
    }

    /**
     * Generate a user object from a successful user details request.
     *
     * @param array $response
     * @param AccessToken $token
     * @return \League\OAuth2\Client\Provider\UserInterface
     */
    protected function createResourceOwner(array $response, AccessToken $token)
    {
        $user = new MicrosoftResourceOwner($response);

        $imageUrl = $this->getUserImageUrl($response, $token);

        return $user->setImageurl($imageUrl);
    }

    /**
     * Get provider url to fetch user details
     *
     * @param  AccessToken $token
     *
     * @return string
     */
    public function getResourceOwnerDetailsUrl(AccessToken $token)
    {
        return 'https://apis.live.net/v5.0/me?access_token='.$token;
    }

    /**
     * Get user image from provider
     *
     * @param  array        $response
     * @param  AccessToken  $token
     *
     * @return array
     */
    protected function getUserImage(array $response, AccessToken $token)
    {
        $url = 'https://apis.live.net/v5.0/'.$response['id'].'/picture';

        $request = $this->getAuthenticatedRequest('get', $url, $token);

        $response = $this->getResponse($request);

        return $response;
    }

    /**
     * Get user image url from provider, if available
     *
     * @param  array        $response
     * @param  AccessToken  $token
     *
     * @return string
     */
    protected function getUserImageUrl(array $response, AccessToken $token)
    {
        $image = $this->getUserImage($response, $token);

        if (isset($image['url'])) {
            return $image['url'];
        }

        return null;
    }
}
