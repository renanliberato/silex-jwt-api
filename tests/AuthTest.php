<?php

use Silex\WebTestCase;

class AuthTest extends WebTestCase
{
    public function createApplication()
    {
        $app = require __DIR__.'/../src/app.php';
        require __DIR__.'/../config/dev.php';
        require __DIR__.'/../src/controllers.php';
        $app['session.test'] = true;

        return $this->app = $app;
    }

    public function testEmptyBodyReturns400()
    {
        $client = $this->createClient();
        $crawler = $client->request('POST', '/auth', array(), array(), array(), 'ofqwjfçowe');

        $this->assertEquals(400, $client->getResponse()->getStatusCode());
    }
}