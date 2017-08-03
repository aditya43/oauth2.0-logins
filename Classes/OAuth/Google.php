<?php

namespace Adi\Classes\OAuth;

use Adi\Classes\Core\Session;
use Adi\Classes\OAuth\HttpRequest;

class Google extends HttpRequest
{
    private $_clientId         = null;
    private $_clientSecret     = null;
    private $_callbackUrl      = null;
    private $_scope            = 'openid email profile';
    private $_requestTokenUrl  = 'https://accounts.google.com/o/oauth2/v2/auth';
    private $_accessTokenUrl   = 'https://www.googleapis.com/oauth2/v4/token';
    private $_checkAccessToken = 'https://www.googleapis.com/oauth2/v3/tokeninfo';

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
        $url .= '&access_type=offline';
        $url .= '&include_granted_scopes=true';
        $url .= '&prompt=consent';

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

        $headers = [
            'headers' => [
                'encoding' => 'application/x-www-form-urlencoded'
            ]
        ];

        $response = $this->_request('POST', $this->_accessTokenUrl, $form_data, $headers);

        if (is_object($response = json_decode($response)))
        {
            if (isset($response->access_token) && isset($response->expires_in))
            {
                Session::put('google', [
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
        $headers = [
            'headers' =>
            [
                'Content-Type' => 'application/x-www-form-urlencoded',
                'Host'         => 'www.googleapis.com'
            ]
        ];

        $response = $this->_request('GET', $this->_checkAccessToken . '?access_token=' . $access_token, [], $headers);

        if (is_object($response = json_decode($response)))
        {
            if (isset($response->expires_in) && !isset($response->error_description) && !isset($response->error))
            {
                if ($response->expires_in < 10)
                {
                    // Access token is about to expire within 10 seconds. Get a new access token from refresh token.
                    return $this->_getAccessToken('refresh_token', $refresh_token);
                }
                else if ($response->expires_in > 10)
                {
                    // Access token is alive and valid. No need to get a new access token. Simply update expiry time.
                    Session::put('google', [
                        'access_token'  => $access_token,
                        'expires_in'    => $response->expires_in,
                        'refresh_token' => $refresh_token
                    ]);
                    return true;
                }
            }
        }
        // Access token expired or invalid. Get a new access token from refresh token.
        return $this->_getAccessToken('refresh_token', $refresh_token);
    }

    protected function _getUserInfo()
    {
        $access_token = Session::get('google')['access_token'];

        $headers = [
            'headers' =>
            [
                'Authorization' => 'Bearer ' . $access_token,
                'Host'          => 'www.googleapis.com'
            ]
        ];

        $userInfo = $this->_request('GET', 'https://www.googleapis.com/oauth2/v3/userinfo?alt=json&access_token=' . $access_token, [], $headers);

        if (is_object($userInfo = json_decode($userInfo)))
        {
            return [
                'uid'    => isset($userInfo->sub) ? $userInfo->sub : null,
                'name'   => isset($userInfo->name) ? $userInfo->name : null,
                'email'  => isset($userInfo->email) ? $userInfo->email : null,
                'avatar' => isset($userInfo->picture) ? $userInfo->picture : null
            ];
        }
        return false;
    }
}
