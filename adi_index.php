<?php
use Adi\Classes\Core\Session;
use Adi\Classes\Core\User;

require 'vendor/autoload.php';
require 'core/init.php';

$tokens = Session::get('facebook');

if ($tokens)
{
    $a = "<script type='text/javascript'>
          console.log('Service : Facebook');";
    if (isset($tokens['code']))
    {
        $a .= "console.log('Code : {$tokens['code']}');";
    }
    if (isset($tokens['access_token']))
    {
        $a .= "console.log('Access Token : {$tokens['access_token']}');
          console.log('Expires In : {$tokens['expires_in']}');
          console.log('Refresh Token : {$tokens['refresh_token']}');";
    }
    $a .= "</script>";
    echo $a;
}

/*+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+*/

$user_google = new User('google');
$googleLogin = User::isLoggedIn('google');

if ((is_array($googleLogin) && 'oauth' == $googleLogin['type']))
{
    $googleUserInfo = $user_google->getInfo('google');
}

if (!is_array($googleLogin) || !$googleUserInfo)
{
    $googleLoginUrl = $user_google->getLoginUrl('google');
}

/*+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+*/

$user_facebook = new User('facebook');
$facebookLogin = User::isLoggedIn('facebook');

if ((is_array($facebookLogin) && 'oauth' == $facebookLogin['type']))
{
    $facebookUserInfo = $user_facebook->getInfo('facebook');
}

if (!is_array($facebookLogin) || !$facebookUserInfo)
{
    $facebookLoginUrl = $user_facebook->getLoginUrl('facebook');
}

/*+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+*/

$user_twitter = new User('twitter');
$twitterLogin = User::isLoggedIn('twitter');

if ((is_array($twitterLogin) && 'oauth' == $twitterLogin['type']))
{
    $twitterUserInfo = $user_twitter->getInfo('twitter');
}

if (!is_array($twitterLogin) || !$twitterUserInfo)
{
    $twitterLoginUrl = $user_twitter->getLoginUrl('twitter');
}

/*+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+*/

$user_linkedin = new User('linkedin');
$linkedinLogin = User::isLoggedIn('linkedin');

if ((is_array($linkedinLogin) && 'oauth' == $linkedinLogin['type']))
{
    $linkedinUserInfo = $user_linkedin->getInfo('linkedin');
}

if (!is_array($linkedinLogin) || !$linkedinUserInfo)
{
    $linkedinLoginUrl = $user_linkedin->getLoginUrl('linkedin');
}

/*+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+*/

$user_microsoft = new User('microsoft');
$microsoftLogin = User::isLoggedIn('microsoft');

if ((is_array($microsoftLogin) && 'oauth' == $microsoftLogin['type']))
{
    $microsoftUserInfo = $user_microsoft->getInfo('microsoft');
}

if (!is_array($microsoftLogin) || !$microsoftUserInfo)
{
    $microsoftLoginUrl = $user_microsoft->getLoginUrl('microsoft');
}

/*+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+*/

$user_yahoo = new User('yahoo');
$yahooLogin = User::isLoggedIn('yahoo');

if ((is_array($yahooLogin) && 'oauth' == $yahooLogin['type']))
{
    $yahooUserInfo = $user_yahoo->getInfo('yahoo');
}

if (!is_array($yahooLogin) || !$yahooUserInfo)
{
    $yahooLoginUrl = $user_yahoo->getLoginUrl('yahoo');
}

/*+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+*/

require 'views/_partials/_header.php';
require 'views/user.php';
require 'views/_partials/_footer.php';
