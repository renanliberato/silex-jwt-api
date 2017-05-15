<?php

use App\DAO\UserDAO;
use App\Service\TokenService;
use Lcobucci\JWT\Builder;
use Silex\Provider\DoctrineServiceProvider;

$db = __DIR__.'/../db/';

if ($app['session.test']) {
    $db .= 'app_test.db';
} else {
    $db .= 'app.db';
}

$app->register(new DoctrineServiceProvider(), array(
    'db.options' => array(
        'driver'   => 'pdo_sqlite',
        'path'     => $db,
    ),
));
$app['db']->connect();

$app['app.service.tokenService'] = $app->factory(function() {
    $key = parse_ini_file(__DIR__.'/../config/settings.ini')['key'];

    return new TokenService($key);
});

$app['app.dao.userDAO'] = $app->factory(function($app) {

    return new UserDAO($app['db']);
});

