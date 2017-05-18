<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

$app->before('controller.authorize:process');

$app->options('/auth', function(){
    return new Response();
});

$app->post('/auth', "controller.authenticate:process");

$app->error(function (\Exception $e, Request $request, $code) use ($app) {

    if ($app['debug']) {
        return;
    }
    return new Response(null, $code);
});