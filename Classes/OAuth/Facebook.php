<?php

namespace Adi\Classes\OAuth;

use Adi\Classes\Core\Session;
use Adi\Classes\OAuth\HttpRequest;

class Facebook extends HttpRequest
{
    private $_clientId         = null;
    private $_clientSecret     = null;
    private $_callbackUrl      = null;
    private $_scope            = 'email public_profile';
    private $_requestTokenUrl  = 'https://www.facebook.com/v2.11/dialog/oauth';
    private $_accessTokenUrl   = 'https://graph.facebook.com/v2.11/oauth/access_token';
    private $_refreshTokenUrl  = 'https://graph.facebook.com/oauth/client_code';
    private $_checkAccessToken = 'https://graph.facebook.com/debug_token';

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
        $url .= '&display=popup';
        $url .= '&granted_scopes=true';

        return $url;
    }

    protected function _getAccessToken($type, $token)
    {
        switch ($type)
        {
            case 'code':
                $url = $this->_accessTokenUrl;
                $url .= '?client_id=' . $this->_clientId;
                $url .= '&client_secret=' . $this->_clientSecret;
                $url .= '&redirect_uri=' . urlencode($this->_callbackUrl);
                $url .= '&code=' . $token;
                break;

            case 'refresh_token';
                $url = $this->_refreshTokenUrl;
                $url .= '?client_id=' . $this->_clientId;
                $url .= '&client_secret=' . $this->_clientSecret;
                $url .= '&redirect_uri=' . urlencode($this->_callbackUrl);
                $url .= '&access_token=' . $token;
                break;
        }

        $headers = [
            'headers' => [
                'encoding' => 'application/x-www-form-urlencoded'
            ]
        ];

        if ('refresh_token' == $type)
        {
            if (!$this->_isAccessTokenValid($token, $token))
            {
                return false;
            }
            $headers['headers']['Authorization'] = 'Bearer ' . $token;
        }

        $response = $this->_request('GET', $url);

        if (is_object($response = json_decode($response)))
        {
            if (isset($response->code) && $response->code)
            {
                return $this->_getAccessToken('code', $response->code);
            }
            else if (isset($response->access_token) && isset($response->expires_in))
            {
                Session::put('facebook', [
                    'access_token'  => $response->access_token,
                    'expires_in'    => $response->expires_in,
                    'refresh_token' => $response->access_token
                ]);

                if ($response->expires_in < 50)
                {
                    // Get a long living or refresh token.
                    return $this->_getAccessToken('refresh_token', $response->access_token);
                }
                return true;
            }
        }
        return false; // Failed to acquire access token.
    }

    protected function _isAccessTokenValid(string $access_token, string $refresh_token)
    {
        $url = $this->_checkAccessToken;
        $url .= '?input_token=' . $access_token;
        $url .= '&access_token=' . $access_token;

        $response = $this->_request('GET', $url);

        if (is_object($response = json_decode($response)))
        {
            if (isset($response->data->expires_at))
            {
                if ($response->data->is_valid && $response->data->expires_at < 10)
                {
                    // Access token is about to expire within 10 seconds. Get a new access token.
                    return $this->_getAccessToken('refresh_token', $refresh_token);
                }
                else if ($response->data->is_valid && $response->data->expires_at > 10)
                {
                    // Access token is alive and valid. No need to get a new access token. Simply update expiry time.
                    Session::put('facebook', [
                        'access_token'  => $access_token,
                        'expires_in'    => $response->data->expires_at,
                        'refresh_token' => $refresh_token
                    ]);
                    return true;
                }
            }
        }
        // Access token expired or invalid. Get a new access token.
        return false;
    }

    protected function _getUserInfo()
    {
        $access_token = Session::get('facebook')['access_token'];

        $headers = [
            'headers' =>
            [
                'Authorization' => 'Bearer ' . $access_token,
                'Host'          => 'https://localhost'
            ]
        ];
        $url = 'https://graph.facebook.com/v2.11/me?fields=email,picture.type(large),name&access_token=' . $access_token;

        $userInfo = $this->_request('GET', $url, [], $headers);

        if (is_object($userInfo = json_decode($userInfo)))
        {
            return [
                'uid'    => isset($userInfo->id) ? $userInfo->id : null,
                'name'   => isset($userInfo->name) ? $userInfo->name : null,
                'email'  => isset($userInfo->email) ? $userInfo->email : null,
                'avatar' => isset($userInfo->picture->data->url) ? $userInfo->picture->data->url : null
            ];
        }
        return false;
    }
}
