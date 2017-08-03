<?php

require "../vendor/autoload.php";

use Adi\Classes\Core\Session;

if (preg_match('/(\w)+/', $_GET['service']))
{
    if (Session::exists($_GET['service']))
    {
        Session::delete($_GET['service']);
    }
}

header('Location: ../adi_index.php');
