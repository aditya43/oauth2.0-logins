<?php

namespace Adi\Classes\OAuth;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Psr7;

abstract class HttpRequest
{
    private static $_guzzleInstance = null;

    private static function _getGuzzleInstance()
    {
        if (null == self::$_guzzleInstance)
        {
            self::$_guzzleInstance = new Client([
                'headers' => [
                    'Content-Type' => 'application/x-www-form-urlencoded'
                ]
            ]);
        }
        return self::$_guzzleInstance;
    }

    protected function _request($type, $url, $data = [], $headers = [])
    {
        $client = self::_getGuzzleInstance();

        try {
            $response = $client->request($type, $url, $data, $headers);
        }
        catch (ClientException $e)
        {
            echo "Client Error : " . $e->getResponse()->getBody(true)->getContents();
            echo "<script type='text/javascript'>
                      console.log('Client Error | Response Code : {$e->getCode()}');
                      console.log({$e->getResponse()->getBody(true)->getContents()});
                  </script>";
            return false;
        }
        catch (ServerException $e)
        {
            echo "<script type='text/javascript'>
                      console.log('Server Error | Response Code : {$e->getCode()}');
                      console.log({$e->getResponse()->getBody(true)->getContents()});
                  </script>";
            return false;
        }
        return isset($response) ? $response->getBody()->getContents() : false;
    }

    public function callApi($token, $action)
    {
        switch ($token['type'])
        {
            case 'access_token':
                $valid = $this->_isAccessTokenValid($token['access_token'], $token['refresh_token']);
                if (false == $valid)
                {
                    return false; // Access token expired or invalid.
                }
                break;

            case 'access_secret':
                $valid = $this->_isAccessTokenValid($token['access_token'], $token['access_secret']);
                if (false == $valid)
                {
                    return false; // Access token expired or invalid.
                }
                break;

            case 'refresh_token':
                $res = $this->_getAccessToken('refresh_token', $token['refresh_token']);
                if (!$res)
                {
                    return false; // Failed to get access token from refresh token.
                }
                break;

            case 'code':
                $this->_getAccessToken('code', $token['code']);
                break;

            case 'oauth_verifier':
                $this->_getAccessToken('oauth_verifier', $token);
                break;
        }

        if (method_exists($this, $action))
        {
            return $this->$action();
        }
    }
}
