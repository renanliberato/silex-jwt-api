<?php

use App\Controller\AuthController;

//Request::setTrustedProxies(array('127.0.0.1'));

/** @var Application $app
 * @return AuthController
 */
$app['auth.controller'] = function() use ($app) {
    return new AuthController($app['app.service.tokenService'], $app['app.dao.userDAO']);
};
