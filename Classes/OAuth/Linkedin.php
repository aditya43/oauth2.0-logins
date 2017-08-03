<?php

namespace Adi\Classes\OAuth;

use Adi\Classes\Core\Session;
use Adi\Classes\OAuth\HttpRequest;

class Linkedin extends HttpRequest
{
    private $_clientId        = null;
    private $_clientSecret    = null;
    private $_callbackUrl     = null;
    private $_scope           = 'r_basicprofile r_emailaddress';
    private $_requestTokenUrl = 'https://www.linkedin.com/oauth/v2/authorization';
    private $_accessTokenUrl  = 'https://www.linkedin.com/oauth/v2/accessToken';

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

        return $url;
    }

    protected function _getAccessToken($type, $token)
    {
        if ('refresh_token' == $type)
        {
            // Access token and refresh token are same so set refresh token as access token and return true.
            Session::put('linkedin', [
                'access_token'  => $token,
                'refresh_token' => $token
            ]);
            return true;
        }

        $form_data = [
            'form_params' => [
                'code'          => $token,
                'client_id'     => $this->_clientId,
                'client_secret' => $this->_clientSecret,
                'redirect_uri'  => $this->_callbackUrl,
                'grant_type'    => 'authorization_code'
            ]
        ];

        $headers = [
            'headers' => [
                'encoding' => 'application/x-www-form-urlencoded',
                'Host'     => 'localhost'
            ]
        ];

        $response = $this->_request('POST', $this->_accessTokenUrl, $form_data, $headers);

        if (is_object($response = json_decode($response)))
        {
            if (isset($response->access_token) && isset($response->expires_in))
            {
                Session::put('linkedin', [
                    'access_token'  => $response->access_token,
                    'expires_in'    => $response->expires_in,
                    'refresh_token' => $response->access_token //Access token lasts 60 days so lets use it as a refresh token.
                ]);
                return true;
            }
        }
        return false; // Failed to acquire access token.
    }

    protected function _isAccessTokenValid()
    {
        // Linkedin doesn't provide way to validate access token.
        // Linkedin doesn't provide refresh tokens. So we can't acquire new access token anyway.
        // Thats why assume access token is valid and alive.
        return true;
    }

    protected function _getUserInfo()
    {
        $access_token = Session::get('linkedin')['access_token'];

        $headers = [
            'headers' =>
            [
                'Authorization' => 'Bearer ' . $access_token,
                'Host'          => 'api.linkedin.com',
                'Connection'    => 'Keep-Alive'
            ]
        ];

        $userInfo = $this->_request('GET', 'https://api.linkedin.com/v1/people/~:(email-address,id,formatted-name,picture-urls::(original))?format=json&oauth2_access_token=' . $access_token, [], $headers);

        if (is_object($userInfo = json_decode($userInfo)))
        {
            return [
                'uid'    => isset($userInfo->id) ? $userInfo->id : null,
                'name'   => isset($userInfo->formattedName) ? $userInfo->formattedName : null,
                'email'  => isset($userInfo->emailAddress) ? $userInfo->emailAddress : null,
                'avatar' => isset($userInfo->pictureUrls->values[0]) ? $userInfo->pictureUrls->values[0] : null
            ];
        }
        return false;
    }
}
