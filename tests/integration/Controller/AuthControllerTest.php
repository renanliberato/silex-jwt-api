<?php

use Lcobucci\JWT\Parser;

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

    public function testAccessTokenReturned()
    {
        $body = [
            'username' => 'superuser',
            'password' => 'superuser'
        ];

        $client = $this->createClient();
        $client->request('POST', '/auth', array(), array(), array(), json_encode($body));

        $bodyObject = json_decode((string) $client->getResponse()->getContent());
        
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertObjectHasAttribute('access', $bodyObject);

        $accessToken = $bodyObject->access;

        $this->assertInternalType('string', $accessToken);

        return $accessToken;
    }

    /**
     * @depends testAccessTokenReturned
     */
    public function testAccessTokenIsValid($accessToken)
    {
        $this->assertEquals(3, count(explode('.', $accessToken)));

        $token  = (new Parser())->parse($accessToken);
        $claims = $token->getClaims();
        
        $this->arrayHasKey('iat', $claims);
        $this->arrayHasKey('exp', $claims);
        $this->arrayHasKey('username', $claims);
    }

    public function testRenewTokenReturned()
    {
        $body = [
            'username' => 'superuser',
            'password' => 'superuser'
        ];

        $client = $this->createClient();
        $client->request('POST', '/auth', array(), array(), array(), json_encode($body));

        $bodyObject = json_decode((string) $client->getResponse()->getContent());
        
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertObjectHasAttribute('renew', $bodyObject);

        $renewToken = $bodyObject->renew;

        $this->assertInternalType('string', $renewToken);

        return $renewToken;
    }

    /**
     * @depends testRenewTokenReturned
     */
    public function testRenewTokenIsValid($renewToken)
    {
        $this->assertEquals(3, count(explode('.', $renewToken)));

        $token  = (new Parser())->parse($renewToken);
        $claims = $token->getClaims();
        
        $this->arrayHasKey('iat', $claims);
        $this->arrayHasKey('exp', $claims);
        $this->arrayHasKey('username', $claims);
    }

}