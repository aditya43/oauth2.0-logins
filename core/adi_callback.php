<?php
require "../vendor/autoload.php";

use Adi\Classes\Core\Session;

if ((isset($_GET['code']) || isset($_GET['oauth_verifier']) || isset($_GET['state'])))
{
    $service = (isset($_GET['state']) && preg_match('/(\w)+/', $_GET['state'])) ? $_GET['state'] : '';
    $service = (isset($_GET['service']) && preg_match('/(\w)+/', $_GET['service'])) ? $_GET['service'] : $service;

    if (isset($_GET['code']) && !empty($_GET['code']))
    {
        Session::put($service, ['code' => $_GET['code']]);
    }
    else if (isset($_GET['oauth_verifier']) && !empty($_GET['oauth_verifier']))
    {
        $tokens = Session::get($service);

        $tokens['oauth_token']    = $_GET['oauth_token'];
        $tokens['oauth_verifier'] = $_GET['oauth_verifier'];

        Session::put($service, $tokens);
    }

    echo "<script type='text/javascript'>
              window.opener.location.reload();
              window.close();
              </script>
              Redirecting..";
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Adi OAuth Callback | Aditya Hajare</title>
</head>
<noscript>
  <meta http-equiv="refresh" content="0;URL='https://localhost/learn/php/03-Adi_OAuth/adi_index.php'">
</noscript>
<body>
Redirecting..
</body>
</html>
