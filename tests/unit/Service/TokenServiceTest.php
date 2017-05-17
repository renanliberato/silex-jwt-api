<?php

namespace AppTest\Service;

use App\Service\ClockInterface;
use App\Service\TokenService;
use \InvalidArgumentException;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Token;
use Mockery;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\HeaderBag;
use Symfony\Component\HttpFoundation\Request;

class TokenServiceTest extends TestCase
{
    public function testGetAccessTokenReturnsAcordingToParams()
    {
        $now        = '1';
        $expiration = '6';
        $username   = 'test';
        $key        = 'abc';

        $clockMock = Mockery::mock(ClockInterface::class);
        $clockMock->shouldReceive('now')->andReturn($now);
        $clockMock->shouldReceive('plusFiveMinutes')->andReturn($expiration);

        $tokenService = new TokenService($clockMock, $key);

        $accessToken = $tokenService->getAccessToken($username);

        $this->assertNotNull($accessToken);
        $this->assertInstanceOf(Token::class, $accessToken);
        $this->assertEquals($accessToken->getClaim('exp'), $expiration);
        $this->assertEquals($accessToken->getClaim('iat'), $now);
        $this->assertEquals($accessToken->getClaim('username'), $username);
        $this->assertTrue($accessToken->verify(new Sha256(), $key));
    }

    public function testGetRenewTokenReturnsAcordingToParams()
    {
        $now        = '1';
        $expiration = '6';
        $username   = 'test';
        $key        = 'abc';

        $clockMock = Mockery::mock(ClockInterface::class);
        $clockMock->shouldReceive('now')->andReturn($now);
        $clockMock->shouldReceive('plusTwentyMinutes')->andReturn($expiration);

        $tokenService = new TokenService($clockMock, $key);

        $renewToken = $tokenService->getRenewToken($username);

        $this->assertNotNull($renewToken);
        $this->assertInstanceOf(Token::class, $renewToken);
        $this->assertEquals($renewToken->getClaim('exp'), $expiration);
        $this->assertEquals($renewToken->getClaim('iat'), $now);
        $this->assertEquals($renewToken->getClaim('username'), $username);
        $this->assertTrue($renewToken->verify(new Sha256(), $key));
    }

    public function testGetFromRequestReturnsNullIfWithoutAuthorizatoinHeader()
    {
        // $requestMock->headers = new HeaderBag([
        //     'Authorization' => 'Bearer ro1i23jr1o23ijr12orij12.1o234j1o23i4j.io123j41o23ij4'
        // ]);

        $tokenService = new TokenService(Mockery::mock(ClockInterface::class), 'abc');

        $requestMock = Mockery::mock(Request::class);
        $requestMock->headers = new HeaderBag([]);

        $tokenFromRequest = $tokenService->getFromRequest($requestMock);

        $this->assertNull($tokenFromRequest);
    }

    public function testGetFromRequestReturnsNullIfWithoutToken()
    {
        $tokenService = new TokenService(Mockery::mock(ClockInterface::class), 'abc');

        $requestMock = Mockery::mock(Request::class);
        $requestMock->headers = new HeaderBag([
            'Authorization' => ''
        ]);

        $tokenFromRequest = $tokenService->getFromRequest($requestMock);

        $this->assertNull($tokenFromRequest);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testGetFromRequestThrowsExceptionIfWithInvalidToken()
    {
        $tokenService = new TokenService(Mockery::mock(ClockInterface::class), 'abc');
        
        $requestMock = Mockery::mock(Request::class);
        $requestMock->headers = new HeaderBag([
            'Authorization' => 'tokenwithoutdots'
        ]);

        $tokenService->getFromRequest($requestMock);
    }

    public function testGetFromRequestReturnsTokenObjectIfValidTokenIsPassed()
    {
        $tokenService = new TokenService(Mockery::mock(ClockInterface::class), 'abc');
        
        $requestMock = Mockery::mock(Request::class);
        $requestMock->headers = new HeaderBag([
            'Authorization' => (string)(new Builder())->getToken()
        ]);

        $token = $tokenService->getFromRequest($requestMock);

        $this->assertNotNull($token);
        $this->assertInternalType('object', $token);
        $this->assertInstanceOf(Token::class, $token);
    }

    public function testIsNotAuthorizedIfAnModifiedTokenIsPassed()
    {
        $key = 'abc';

        $tokenService = new TokenService(Mockery::mock(ClockInterface::class), $key);
        
        $originalTokenString = (string)(new Builder())
            ->setExpiration(strtotime('now +1 minute'))
            ->sign(new Sha256(), $key)
            ->getToken();

        $originalTokenStringExploded = explode('.', $originalTokenString);

        $claims = json_decode(base64_decode($originalTokenStringExploded[1]));

        $claims->exp = strtotime('now +5 minutes');

        $originalTokenStringExploded[1] = base64_encode(json_encode($claims));

        $modifiedToken = (new Parser())->parse(implode('.', $originalTokenStringExploded));

        $isAuthorized = $tokenService->isAuthorized($modifiedToken);

        $this->assertFalse($isAuthorized);
    }

    public function testIsNotAuthorizedIfAnExpiredTokenIsPassed()
    {
        $key = 'abc';
        $tokenService = new TokenService(Mockery::mock(ClockInterface::class), $key);
        
        $expiredToken = (new Builder())
            ->setExpiration(strtotime('now -1 minute'))
            ->sign(new Sha256(), $key)
            ->getToken();

        $isAuthorized = $tokenService->isAuthorized($expiredToken);

        $this->assertFalse($isAuthorized);
    }

    public function testIsValidIfAnAuthenticTokenAndNotExpiredIsPassed()
    {
        $key = 'abc';
        $tokenService = new TokenService(Mockery::mock(ClockInterface::class), $key);
        
        $expiredToken = (new Builder())
            ->setExpiration(strtotime('now +1 minute'))
            ->sign(new Sha256(), $key)
            ->getToken();

        $isAuthorized = $tokenService->isAuthorized($expiredToken);

        $this->assertTrue($isAuthorized);
    }
}