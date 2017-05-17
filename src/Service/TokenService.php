<?php

namespace App\Service;

use App\Service\ClockInterface;
use \InvalidArgumentException;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Signer;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Token;
use Symfony\Component\HttpFoundation\Request;

class TokenService
{

    /**
     * Clock service
     *
     * @var ClockInterface
     */
    private $clock;

    /**
     * Sign key
     *
     * @var string
     */
    private $key;

    /**
     * Signer service
     *
     * @var Signer
     */
    private $signer;

    public function __construct(ClockInterface $clock, $key)
    {
        $this->signer = new Sha256();
        $this->clock  = $clock;
        $this->key    = $key;
    }

    public function getAccessToken($username)
    {
        $builder = new Builder();
        $builder
            ->setIssuedAt($this->clock->now())
            ->setExpiration($this->clock->plusFiveMinutes())
            ->set('username', $username)
            ->sign($this->signer, $this->key);

        return $builder->getToken();
    }

    public function getRenewToken($username)
    {
        $builder = new Builder();
        $builder
            ->setIssuedAt($this->clock->now())
            ->setExpiration($this->clock->plusTwentyMinutes())
            ->set('username', $username)
            ->sign($this->signer, $this->key);

        return $builder->getToken();
    }

    /**
     * Retrieve a Token object from "Authorization" Request header.
     *
     * @param Request $request
     * 
     * @return null | Token
     * 
     * @throws InvalidArgumentException
     * 
     */
    public function getFromRequest(Request $request)
    {
        $authorizationHeader = $request->headers->get('Authorization');
        if (!$authorizationHeader) {
            return null;
        }

        $stringToken = str_replace('Bearer ', '', $authorizationHeader);

        if (!$stringToken) {
            return null;
        }

        return (new Parser())->parse($stringToken);
    }

    /**
     * Check if the token was not modified and is not expired.
     *
     * @param Token $token
     * 
     * @return boolean
     */
    public function isAuthorized(Token $token)
    {
        try {
            if (!$token->verify($this->signer, $this->key)) {
                return false;
            }

            if ($token->isExpired()) {
                return false;
            }

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}