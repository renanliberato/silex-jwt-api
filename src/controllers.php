<?php

use App\Controller\Authenticate;
use App\Controller\Authorize;

//Request::setTrustedProxies(array('127.0.0.1'));

/** @var Application $app
 * @return Authenticate
 */
$app['controller.authenticate'] = function() use ($app) {
    return new Authenticate($app['app.service.tokenService'], $app['app.dao.userDAO']);
};

/** @var Application $app
 * @return Authorize
 */
$app['controller.authorize'] = function() use ($app) {
    return new Authorize($app['app.service.tokenService']);
};
