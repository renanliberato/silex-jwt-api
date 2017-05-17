<?php

use App\Controller\Authenticate;
use App\DAO\UserDAO;
use App\Service\TokenService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateTest extends TestCase
{
    public function testEmptyPostShouldReturn401Response()
    {
        $tokenServiceMock = Mockery::mock(TokenService::class);
        $userDAOMock      = Mockery::mock(UserDAO::class);
        $authController   = new Authenticate($tokenServiceMock, $userDAOMock);

        $requestMock = Mockery::mock(Request::class);
        $requestMock->shouldReceive('getContent')->andReturn(null);

        $response = $authController->process($requestMock);

        $this->assertEquals(401, $response->getStatusCode());
    }

    public function testThrowedExceptionOnUserSearchShouldReturn401Response()
    {
        $tokenServiceMock = Mockery::mock(TokenService::class);
        $userDAOMock      = Mockery::mock(UserDAO::class);
        $userDAOMock->shouldReceive('getByUsernameAndPassword')->andThrow(new \Exception());

        $authController = new Authenticate($tokenServiceMock, $userDAOMock);

        $requestMock = Mockery::mock(Request::class);
        $requestMock->shouldReceive('getContent')->andReturn(json_encode([
            'username' => 'any',
            'password' => 'any'
        ]));

        $response = $authController->process($requestMock);

        $this->assertEquals(401, $response->getStatusCode());
    }

    public function testNotFoundUserShouldReturn401Response()
    {
        $tokenServiceMock = Mockery::mock(TokenService::class);
        $userDAOMock      = Mockery::mock(UserDAO::class);
        $userDAOMock->shouldReceive('getByUsernameAndPassword')->andReturn(null);

        $authController = new Authenticate($tokenServiceMock, $userDAOMock);

        $requestMock = Mockery::mock(Request::class);
        $requestMock->shouldReceive('getContent')->andReturn(json_encode([
            'username' => 'any',
            'password' => 'any'
        ]));

        $response = $authController->process($requestMock);

        $this->assertEquals(401, $response->getStatusCode());
    }

    public function testThrowedExceptionOnAccessTokenCreationShouldReturn401Response()
    {
        $tokenServiceMock = Mockery::mock(TokenService::class);
        $tokenServiceMock->shouldReceive('getAccessToken')->andThrow(new \Exception());
        $tokenServiceMock->shouldReceive('getRenewToken')->andReturn(true);

        $userDAOMock      = Mockery::mock(UserDAO::class);
        $userDAOMock->shouldReceive('getByUsernameAndPassword')->andReturn(true);

        $authController = new Authenticate($tokenServiceMock, $userDAOMock);

        $requestMock = Mockery::mock(Request::class);
        $requestMock->shouldReceive('getContent')->andReturn(json_encode([
            'username' => 'any',
            'password' => 'any'
        ]));

        $response = $authController->process($requestMock);

        $this->assertEquals(401, $response->getStatusCode());
    }

    public function testThrowedExceptionOnRenewTokenCreationShouldReturn401Response()
    {
        $tokenServiceMock = Mockery::mock(TokenService::class);
        $tokenServiceMock->shouldReceive('getAccessToken')->andReturn(true);
        $tokenServiceMock->shouldReceive('getRenewToken')->andThrow(new \Exception());

        $userDAOMock      = Mockery::mock(UserDAO::class);
        $userDAOMock->shouldReceive('getByUsernameAndPassword')->andReturn(true);

        $authController = new Authenticate($tokenServiceMock, $userDAOMock);

        $requestMock = Mockery::mock(Request::class);
        $requestMock->shouldReceive('getContent')->andReturn(json_encode([
            'username' => 'any',
            'password' => 'any'
        ]));

        $response = $authController->process($requestMock);

        $this->assertEquals(401, $response->getStatusCode());
    }

    public function testFoundUserShouldReturnTokens()
    {
        $accessToken = 'accesstoken';
        $renewToken  = 'renewtoken';
        $tokenServiceMock = Mockery::mock(TokenService::class);
        $tokenServiceMock->shouldReceive('getAccessToken')->andReturn($accessToken);
        $tokenServiceMock->shouldReceive('getRenewToken')->andReturn($renewToken);
        
        $userDAOMock = Mockery::mock(UserDAO::class);
        $userDAOMock->shouldReceive('getByUsernameAndPassword')->andReturn(true);

        $authController = new Authenticate($tokenServiceMock, $userDAOMock);

        $requestMock = Mockery::mock(Request::class);
        $requestMock->shouldReceive('getContent')->andReturn(json_encode([
            'username' => 'any',
            'password' => 'any'
        ]));

        $response = $authController->process($requestMock);
        
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertInternalType('string', $response->getContent());
        $this->assertNotEmpty($response->getContent());

        $content = json_decode($response->getContent());

        $this->objectHasAttribute('access', $content);
        $this->objectHasAttribute('renew', $content);
        $this->assertEquals($accessToken, $content->access);
        $this->assertEquals($renewToken, $content->renew);
    }
}