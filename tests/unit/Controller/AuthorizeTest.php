<?php

use App\Controller\Authorize;
use App\DAO\UserDAO;
use App\Service\TokenService;
use Lcobucci\JWT\Token;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthorizeTest extends TestCase
{
    public function testAuthPathInfoShouldReturnNull()
    {
        $tokenServiceMock = Mockery::mock(TokenService::class);
        $authorizeController = new Authorize($tokenServiceMock);

        $requestMock = Mockery::mock(Request::class);
        $requestMock->shouldReceive('getPathInfo')->andReturn('/auth');

        $response = $authorizeController->process($requestMock);

        $this->assertNull($response);
    }

    public function testNotAuthorizedTokenShouldReturn401Response()
    {
        $tokenServiceMock = Mockery::mock(TokenService::class);
        $tokenServiceMock->shouldReceive('getFromRequest')->andReturn(Mockery::mock(Token::class));
        $tokenServiceMock->shouldReceive('isAuthorized')->andReturn(false);
        $authorizeController = new Authorize($tokenServiceMock);

        $requestMock = Mockery::mock(Request::class);
        $requestMock->shouldReceive('getPathInfo')->andReturn('/');

        $response = $authorizeController->process($requestMock);

        $this->assertEquals(401, $response->getStatusCode());
    }

    public function testAuthorizedTokenShouldReturnNull()
    {
        $tokenServiceMock = Mockery::mock(TokenService::class);
        $tokenServiceMock->shouldReceive('getFromRequest')->andReturn(Mockery::mock(Token::class));
        $tokenServiceMock->shouldReceive('isAuthorized')->andReturn(true);
        $authorizeController = new Authorize($tokenServiceMock);

        $requestMock = Mockery::mock(Request::class);
        $requestMock->shouldReceive('getPathInfo')->andReturn('/');

        $response = $authorizeController->process($requestMock);

        $this->assertNull($response);
    }
}
