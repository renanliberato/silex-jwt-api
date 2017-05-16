<?php

namespace AppTest\Service;

use App\Service\ClockInterface;
use App\Service\TokenService;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Token;
use Mockery;
use PHPUnit\Framework\TestCase;

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
}