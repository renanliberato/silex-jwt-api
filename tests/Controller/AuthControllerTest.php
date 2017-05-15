<?php

namespace AppTest\Controller;

class AuthTest extends AbstractTest
{
    public function testInvalidMethodGet()
    {
        $client = $this->createClient();
        $client->request('GET', '/auth');

        $this->assertEquals(405, $client->getResponse()->getStatusCode());
    }

    public function testEmptyPost()
    {
        $client = $this->createClient();
        $client->request('POST', '/auth');

        $this->assertEquals(401, $client->getResponse()->getStatusCode());
    }

    public function testInvalidUser()
    {
        $body = [
            'username' => 'myinvaliduser',
            'password' => 'myinvalidpassword'
        ];

        $client   = $this->createClient();
        $client->request('POST', '/auth', array(), array(), array(), json_encode($body));

        $this->assertEquals(401, $client->getResponse()->getStatusCode());
    }

    public function testValidUserAndInvalidPassword()
    {
        $body = [
            'username' => 'superuser',
            'password' => 'alsçjdfald'
        ];

        $client = $this->createClient();
        $client->request('POST', '/auth', array(), array(), array(), json_encode($body));

        $this->assertEquals(401, $client->getResponse()->getStatusCode());
    }

    public function testValidUserAndValidPassword()
    {
        $body = [
            'username' => 'superuser',
            'password' => 'superuser'
        ];

        $client = $this->createClient();
        $client->request('POST', '/auth', array(), array(), array(), json_encode($body));

        $bodyObject = json_decode((string) $client->getResponse()->getContent());
        
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
}