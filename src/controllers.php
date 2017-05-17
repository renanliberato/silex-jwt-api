<?php

use App\Controller\Authenticate;

//Request::setTrustedProxies(array('127.0.0.1'));

/** @var Application $app
 * @return Authenticate
 */
$app['controller.authenticate'] = function() use ($app) {
    return new Authenticate($app['app.service.tokenService'], $app['app.dao.userDAO']);
};