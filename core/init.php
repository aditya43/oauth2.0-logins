<?php

use Adi\Classes\Core\App;
use Adi\Classes\Core\Database;

App::bind('config', require 'config.php');
App::bind('database', Database::getInstance(App::get('config')['database']));
