<?php

namespace Adi\Classes\OAuth;

require 'OAuth.php';

use Adi\Classes\Core\Session;
use Adi\Classes\OAuth\HttpRequest;
use Adi\Classes\OAuth\OAuth;

class Twitter extends HttpRequest
{
    private $_clientId     = null;
    private $_clientSecret = null;
    private $_callbackUrl  = null;

    public $_sha1_method        = null;
    public $_consumer           = null;
    public $_token              = null;
    public $authorized_verifier = null;

    private $_requestTokenUrl   = 'https://api.twitter.com/oauth/request_token';
    private $_authorizeTokenUrl = 'https://api.twitter.com/oauth/authorize';
    private $_accessTokenUrl    = 'https://api.twitter.com/oauth/access_token';
    private $_checkAccessToken  = 'https://api.twitter.com/1.1/account/verify_credentials.json';

    public function __construct($config = [])
    {
        $this->_clientId     = $config['clientId'];
        $this->_clientSecret = $config['clientSecret'];
        $this->_callbackUrl  = $config['callbackUrl'];
    }

    private function _init_params()
    {
        $this->_sha1_method = new OAuthSignatureMethod_HMAC_SHA1();
        $this->_consumer    = new OAuthConsumer($this->_clientId, $this->_clientSecret, null);
    }

    public function getLoginUrl()
    {
        $this->_init_params();

        $rawResponse = $this->_sendRequest($this->_requestTokenUrl, ['oauth_callback' => $this->_callbackUrl]);
        $response    = (object) $this->_parseResponse($rawResponse);

        if (!isset($response->oauth_token))
        {
            return false;
        }

        Session::put('twitter', [
            'oauth_token'        => $response->oauth_token,
            'oauth_token_secret' => $response->oauth_token_secret
        ]);

        $url = $this->_authorizeTokenUrl;
        $url .= "?oauth_callback=" . urlencode($this->_callbackUrl);
        $url .= "&oauth_token=" . $response->oauth_token;
        $url .= "&force_login=false";

        return $url;
    }

    protected function _getAccessToken($type, $token)
    {
        $this->_init_params();
        $this->authorized_verifier = $token['oauth_verifier'];

        $this->_token = new OAuthConsumer($token['oauth_token'], $token['oauth_token_secret']);

        $rawResponse = $this->_sendRequest($this->_accessTokenUrl, ['oauth_verifier' => $token['oauth_verifier']]);
        $response    = (object) $this->_parseResponse($rawResponse);

        if (!isset($response->oauth_token))
        {
            return false;
        }

        $tokens = new OAuthConsumer($response->oauth_token, $response->oauth_token_secret);
        Session::put('twitter', [
            'access_token'  => $tokens->key,
            'access_secret' => $tokens->secret
        ]);

        return true;
    }

    protected function _isAccessTokenValid(string $access_token, string $access_secret)
    {
        // Twitter never expire access tokens, so no need to check if it is alive or not.
        // Assume access token is valid and alive.
        Session::put('twitter', [
            'access_token'  => $access_token,
            'access_secret' => $access_secret
        ]);

        return true;
    }

    protected function _getUserInfo()
    {
        $this->_init_params();
        $tokens = Session::get('twitter');

        $this->_token = new OAuthConsumer($tokens['access_token'], $tokens['access_secret']);

        $params      = ['include_email' => 'true', 'include_entities' => 'false', 'skip_status' => 'true'];
        $rawResponse = $this->_sendRequest($this->_checkAccessToken, $params);

        if (is_object($userInfo = json_decode($rawResponse)))
        {
            return [
                'uid'    => isset($userInfo->id_str) ? $userInfo->id_str : null,
                'name'   => isset($userInfo->name) ? $userInfo->name : null,
                'email'  => isset($userInfo->email) ? $userInfo->email : null,
                'avatar' => isset($userInfo->profile_image_url_https) ? str_replace('_normal', '_200x200', $userInfo->profile_image_url_https) : null
            ];
        }
        return false;
    }

    private function _sendRequest($url, $args = [], $method = 'GET')
    {
        $request = OAuthRequest::from_consumer_and_token($this->_consumer, $this->_token, $method, $url, $args);
        $request->sign_request($this->_sha1_method, $this->_consumer, $this->_token);
        return $this->_request('GET', $request->to_url());
    }

    private function _parseResponse($response)
    {
        $result = [];
        foreach (explode('&', $response) as $param)
        {
            $pair = explode('=', $param, 2);
            if (count($pair) != 2)
            {
                continue;
            }
            $result[urldecode($pair[0])] = urldecode($pair[1]);
        }
        return $result;
    }
}
