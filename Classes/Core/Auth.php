<?php
namespace Adi\Classes\Core;

use Adi\Classes\Core\App;
use Adi\Classes\Core\Session;
use Adi\Classes\OAuth\Facebook;
use Adi\Classes\OAuth\Google;
use Adi\Classes\OAuth\Linkedin;
use Adi\Classes\OAuth\Microsoft;
use Adi\Classes\OAuth\Twitter;
use Adi\Classes\OAuth\Yahoo;

class Auth
{
    protected $_serviceInstance = null;
    protected $_service         = null;
    protected $_db              = null;

    public function __construct($service = null)
    {
        $this->_db      = App::get('database');
        $this->_service = $service;

        $config = App::get('config');

        switch ($service)
        {
            case 'google':
                $this->_serviceInstance = new Google($config[$service]);
                break;

            case 'facebook':
                $this->_serviceInstance = new Facebook($config[$service]);
                break;

            case 'twitter':
                $this->_serviceInstance = new Twitter($config[$service]);
                break;

            case 'linkedin':
                $this->_serviceInstance = new Linkedin($config[$service]);
                break;

            case 'microsoft':
                $this->_serviceInstance = new Microsoft($config[$service]);
                break;

            case 'yahoo':
                $this->_serviceInstance = new Yahoo($config[$service]);
                break;

            default:
                $this->_serviceInstance = null; // Without OAuth
                break;
        }
    }

    public function getLoginUrl()
    {
        return $this->_serviceInstance->getLoginUrl();
    }

    public function authenticate($userInfo)
    {
        Session::put('userEmail', $userInfo['email']);
        $tokens = Session::get($this->_service);

        if (!$this->_db)
        {
            $this->_db = App::get('database');
        }

        if (isset($tokens['refresh_token']) && !empty($tokens['refresh_token']))
        {
            if (isset($tokens['xoauth_yahoo_guid']))
            {
                $tokens = [
                    'refresh_token'     => $tokens['refresh_token'],
                    'xoauth_yahoo_guid' => $tokens['xoauth_yahoo_guid']
                ];
            }
            else
            {
                $tokens = [
                    'refresh_token' => $tokens['refresh_token']
                ];
            }
        }
        else if (isset($tokens['access_secret']) && !empty($tokens['access_secret']))
        {
            $tokens = [
                'access_token'  => $tokens['access_token'],
                'access_secret' => $tokens['access_secret']
            ];
        }

        $params = [
            'service'        => $this->_service,
            'service_userid' => $userInfo['uid'],
            'tokens'         => json_encode($tokens),
            'name'           => $userInfo['name'],
            'email'          => $userInfo['email'],
            'avatar'         => $userInfo['avatar']
        ];
        $duplicate = [
            'tokens',
            'avatar'
        ];
        return $this->_db->insert('oauth_users', $params, $duplicate);
    }

    public function getInfo()
    {
        $token    = $this->_getToken();
        $userInfo = $this->_serviceInstance->callApi($token, '_getUserInfo');
        if ($userInfo)
        {
            $this->authenticate($userInfo);
            return $userInfo;
        }
        return false;
    }

    private function _getToken()
    {
        $service_session = Session::get($this->_service);

        if (array_key_exists('access_token', $service_session) && !empty($service_session['access_token']))
        {
            $token = [];
            if (array_key_exists('access_secret', $service_session))
            {
                $token['type']          = 'access_secret';
                $token['access_token']  = $service_session['access_token'];
                $token['access_secret'] = $service_session['access_secret'];

                return $token;
            }
            else
            {
                return $token = [
                    'type'          => 'access_token',
                    'access_token'  => $service_session['access_token'],
                    'refresh_token' => $service_session['refresh_token']
                ];
            }
        }
        else if (array_key_exists('refresh_token', $service_session) && !empty($service_session['refresh_token']))
        {
            return $token = [
                'type'          => 'refresh_token',
                'refresh_token' => $service_session['refresh_token']
            ];
        }
        else if (array_key_exists('code', $service_session) && !empty($service_session['code']))
        {
            Session::delete($this->_service);
            return $token = [
                'type' => 'code',
                'code' => $service_session['code']
            ];
        }
        else if (array_key_exists('oauth_verifier', $service_session) && !empty($service_session['oauth_verifier']))
        {
            Session::delete($this->_service);
            return $token = [
                'type'               => 'oauth_verifier',
                'oauth_token'        => $service_session['oauth_token'],
                'oauth_token_secret' => $service_session['oauth_token_secret'],
                'oauth_verifier'     => $service_session['oauth_verifier']
            ];
        }
        return false;
    }
}
