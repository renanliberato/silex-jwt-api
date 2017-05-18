<?php

ini_set('display_errors', 0);

require_once __DIR__.'/../vendor/autoload.php';

$app = require __DIR__.'/../src/app.php';
require __DIR__.'/../config/prod.php';
require __DIR__.'/../src/services.php';
require __DIR__.'/../src/controllers.php';
require __DIR__.'/../src/routes.php';

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, OPTIONS, HEAD");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

$app->run();
