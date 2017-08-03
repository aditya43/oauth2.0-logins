<?php

namespace Adi\Classes\OAuth;

use Adi\Classes\Core\Session;
use Adi\Classes\OAuth\HttpRequest;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;

class Microsoft extends HttpRequest
{
    private $_clientId        = null;
    private $_clientSecret    = null;
    private $_callbackUrl     = null;
    private $_scope           = 'profile email openid offline_access User.Read User.ReadBasic.All';
    private $_requestTokenUrl = 'https://login.microsoftonline.com/common/oauth2/v2.0/authorize';
    private $_accessTokenUrl  = 'https://login.microsoftonline.com/common/oauth2/v2.0/token';

    public function __construct($config = [])
    {
        $this->_clientId     = $config['clientId'];
        $this->_clientSecret = $config['clientSecret'];
        $this->_callbackUrl  = $config['callbackUrl'];
    }

    public function getLoginUrl()
    {
        $url = $this->_requestTokenUrl;
        $url .= '?client_id=' . $this->_clientId;
        $url .= '&redirect_uri=' . urlencode($this->_callbackUrl);
        $url .= '&scope=' . urlencode($this->_scope);
        $url .= '&response_type=code';
        $url .= '&state=microsoft';

        return $url;
    }

    protected function _getAccessToken($type, $token)
    {
        switch ($type)
        {
            case 'code':
                $form_data = [
                    'form_params' => [
                        'code'          => $token,
                        'client_id'     => $this->_clientId,
                        'client_secret' => $this->_clientSecret,
                        'redirect_uri'  => $this->_callbackUrl,
                        'scope'         => $this->_scope,
                        'grant_type'    => 'authorization_code'
                    ]
                ];
                break;

            case 'refresh_token';
                $form_data = [
                    'form_params' => [
                        'refresh_token' => $token,
                        'client_id'     => $this->_clientId,
                        'client_secret' => $this->_clientSecret,
                        'scope'         => $this->_scope,
                        'redirect_uri'  => $this->_callbackUrl,
                        'grant_type'    => 'refresh_token'
                    ]
                ];
                break;
        }

        $headers = [
            'headers' => [
                'Content-Type' => 'application/x-www-form-urlencoded'
            ]
        ];

        $response = $this->_request('POST', $this->_accessTokenUrl, $form_data, $headers);

        if (is_object($response = json_decode($response)))
        {
            if (isset($response->access_token) && isset($response->expires_in))
            {
                Session::put('microsoft', [
                    'access_token'  => $response->access_token,
                    'expires_in'    => $response->expires_in,
                    'refresh_token' => isset($response->refresh_token) ? $response->refresh_token : $token
                ]);

                return true;
            }
        }
        return false; // Failed to acquire access token.
    }

    protected function _isAccessTokenValid(string $access_token, string $refresh_token)
    {
        // Microsoft doesn't provide way to validate access token.
        // Assume access token is valid and alive.
        return true;
    }

    protected function _getUserInfo()
    {
        $access_token = Session::get('microsoft')['access_token'];

        $headers = [
            'headers' =>
            [
                'Authorization' => "Bearer {$access_token}",
                'Accept'        => 'application/json'
            ]
        ];

        $client   = new Client($headers);
        $response = $client->request('GET', 'https://graph.microsoft.com/v1.0/me?$select=id,mail,displayName,photo&$format=json', [], $headers)->getBody()->getContents();

        if (is_object($userInfo = json_decode($response)))
        {
            return [
                'uid'    => isset($userInfo->id) ? $userInfo->id : null,
                'name'   => isset($userInfo->displayName) ? $userInfo->displayName : null,
                'email'  => isset($userInfo->userPrincipalName) ? $userInfo->userPrincipalName : null,
                'avatar' => null
            ];
        }
        return false;
    }
}
