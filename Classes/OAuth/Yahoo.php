<?php

namespace Adi\Classes\OAuth;

use Adi\Classes\Core\Session;
use Adi\Classes\OAuth\HttpRequest;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;

class Yahoo extends HttpRequest
{
    private $_clientId        = null;
    private $_clientSecret    = null;
    private $_callbackUrl     = null;
    private $_requestTokenUrl = 'https://api.login.yahoo.com/oauth2/request_auth';
    private $_accessTokenUrl  = 'https://api.login.yahoo.com/oauth2/get_token';

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
        $url .= '&response_type=code';
        $url .= '&state=yahoo';

        return $url;
    }

    protected function _getAccessToken($type, $token)
    {
        $headers = [
            'headers' => [
                'Content-Type'  => 'application/x-www-form-urlencoded',
                'Authorization' => 'Basic ' . base64_encode($this->_clientId . ':' . $this->_clientSecret)
            ]
        ];
        switch ($type)
        {
            case 'code':
                $form_data = [
                    'form_params' => [
                        'code'          => $token,
                        'client_id'     => $this->_clientId,
                        'client_secret' => $this->_clientSecret,
                        'redirect_uri'  => $this->_callbackUrl,
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
                        'redirect_uri'  => $this->_callbackUrl,
                        'grant_type'    => 'refresh_token'
                    ]
                ];
                break;
        }

        $response = $this->_request('POST', $this->_accessTokenUrl, $form_data, $headers);

        if (is_object($response = json_decode($response)))
        {
            if (isset($response->access_token) && isset($response->expires_in))
            {
                Session::put('yahoo', [
                    'access_token'      => $response->access_token,
                    'expires_in'        => $response->expires_in,
                    'refresh_token'     => isset($response->refresh_token) ? $response->refresh_token : $token,
                    'xoauth_yahoo_guid' => isset($response->xoauth_yahoo_guid) ? $response->xoauth_yahoo_guid : ''
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
        $tokens = Session::get('yahoo');

        $access_token = $tokens['access_token'];
        $guid         = $tokens['xoauth_yahoo_guid'];

        $headers = [
            'headers' =>
            [
                'Authorization' => "Bearer {$access_token}"
            ]
        ];

        $client   = new Client($headers);
        $response = $client->request('GET', 'https://social.yahooapis.com/v1/user/' . $guid . '/profile?format=json', [], $headers)->getBody()->getContents();

        if (is_object($userInfo = json_decode($response)))
        {
            return [
                'uid'    => isset($userInfo->profile->guid) ? $userInfo->profile->guid : null,
                'name'   => (isset($userInfo->profile->givenName) || isset($userInfo->profile->familyName)) ? $userInfo->profile->givenName . ' ' . $userInfo->profile->familyName : null,
                'email'  => isset($userInfo->profile->emails[0]->handle) ? $userInfo->profile->emails[0]->handle : null,
                'avatar' => isset($userInfo->profile->image->imageUrl) ? $userInfo->profile->image->imageUrl : null
            ];
        }
        return false;
    }
}
