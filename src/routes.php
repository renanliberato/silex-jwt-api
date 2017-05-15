<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

$app->post('/auth', "auth.controller:authenticate");

$app->error(function (\Exception $e, Request $request, $code) use ($app) {

    if ($app['debug']) {
        return;
    }
    return new Response(null, $code);
});