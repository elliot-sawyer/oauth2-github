<?php

namespace League\OAuth2\Client\Provider;

use League\OAuth2\Client\Provider\Exception\TrademeIdentityProviderException;
use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Tool\BearerAuthorizationTrait;
use Psr\Http\Message\ResponseInterface;

class Trademe extends AbstractProvider
{
    use BearerAuthorizationTrait;

    /**
     * Domain
     *
     * @var string
     */
    public $livedomain = 'https://secure.trademe.co.nz';
    public $testdomain = 'https://secure.tmsandbox.co.nz'

    /**
     * Api domain
     *
     * @var string
     */
    public $apiLiveDomain = 'https://api.trademe.co.nz';
    public $apiTestDomain = 'https://api.tmsandbox.co.nz';
    
    public function domain()
    {
        return $this->trademeSandbox === true ? $this->testdomain : $this->livedomain;
    }

    public function apiDomain()
    {
        return $this->trademeSandbox === true ? $this->apiTestDomain : $this->apiLiveDomain;   
    }
    /**
     * Get authorization url to begin OAuth flow
     *
     * @return string
     */
    public function getBaseAuthorizationUrl()
    {
        return $this->domain().'/Oauth/Authorize';
    }

    /**
     * Get access token url to retrieve token
     *
     * @param  array $params
     *
     * @return string
     */
    public function getBaseAccessTokenUrl(array $params)
    {
        return $this->domain().'/Oauth/AccessToken';
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
        return $this->apiDomain().'/v1/MyTradeMe/Summary.json';
    }

    /**
     * Get the default scopes used by this provider.
     *
     * This should not be a complete list of all scopes, but the minimum
     * required for the provider user interface!
     *
     * @return array
     */
    protected function getDefaultScopes()
    {
        return ['MyTradeMeRead'];
    }

    /**
     * Check a provider response for errors.
     *
     * @todo replace Github with Trademe
     * @throws IdentityProviderException
     * @param  ResponseInterface $response
     * @param  array $data Parsed response data
     * @return void
     */
    protected function checkResponse(ResponseInterface $response, $data)
    {
        if ($response->getStatusCode() >= 400) {
            throw TrademeIdentityProviderException::clientException($response, $data);
        } elseif (isset($data['error'])) {
            throw TrademeIdentityProviderException::oauthException($response, $data);
        }
    }

    /**
     * Generate a user object from a successful user details request.
     *
     * @param array $response
     * @param AccessToken $token
     * @return \League\OAuth2\Client\Provider\ResourceOwnerInterface
     */
    protected function createResourceOwner(array $response, AccessToken $token)
    {
        $user = new TrademeResourceOwner($response);

        return $user->setDomain($this->domain());
    }
}
