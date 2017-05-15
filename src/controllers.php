<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
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

//Request::setTrustedProxies(array('127.0.0.1'));

$app->post('/auth', function (Request $request) use ($app) {
    $body = json_decode($request->getContent());

    if (empty($body)) {
        return new Response(null, 400);
    }
});

$app->error(function (\Exception $e, Request $request, $code) use ($app) {
    if ($app['debug']) {
        return;
    }

    return new Response(null, $code);
});
