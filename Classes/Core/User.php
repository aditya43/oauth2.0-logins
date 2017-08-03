<?php

namespace Adi\Classes\Core;

use Adi\Classes\Core\Auth;
use Adi\Classes\Core\Session;

class User
{
    protected static $_userEmail = null;
    private static $_service     = null;
    private $_authInstance       = null;
    private $_userInfo           = null;

    public function __construct($service = null)
    {
        self::$_service      = $service;
        $this->_authInstance = new Auth($service);
    }

    public static function isLoggedIn($service = null)
    {
        if ($service = null && self::$_userEmail = Session::get('userEmail'))
        {
            return self::$_userEmail;
        }
        else if ($service_session = Session::get(self::$_service))
        {
            if ((array_key_exists('code', $service_session) && !empty($service_session['code']))
                || (array_key_exists('access_token', $service_session) && !empty($service_session['access_token']))
                || (array_key_exists('refresh_token', $service_session) && !empty($service_session['refresh_token']))
                || (array_key_exists('oauth_verifier', $service_session) && !empty($service_session['oauth_verifier']))
            )
            {
                return [
                    'userEmail' => self::$_userEmail = Session::get('userEmail'),
                    'type'      => 'oauth'
                ];
            }
            else
            {
                Session::delete(self::$_service);
            }
        }
        return false;
    }

    public function getLoginUrl()
    {
        return $this->_authInstance->getLoginUrl();
    }

    public static function logout()
    {
        Session::delete('userEmail');
    }

    public function getInfo()
    {
        if ($this->_userInfo)
        {
            return $this->_userInfo;
        }

        $this->_userInfo = $this->_authInstance->getInfo();

        if (!$this->_userInfo)
        {
            Session::delete(self::$_service); // Access token or refresh token is present in session but expired.
        }
        return $this->_userInfo;
    }
}
