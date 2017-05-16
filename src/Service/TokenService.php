<?php

namespace App\Service;

use App\Service\ClockInterface;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer;
use Lcobucci\JWT\Signer\Hmac\Sha256;

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

    public function __construct(ClockInterface $clock, $key)
    {
        $this->clock = $clock;
        $this->key = $key;
    }

    public function getAccessToken($username)
    {
        $builder = new Builder();
        $builder
            ->setIssuedAt($this->clock->now())
            ->setExpiration($this->clock->plusFiveMinutes())
            ->set('username', $username)
            ->sign(new Sha256(), $this->key);

        return $builder->getToken();
    }

    public function getRenewToken($username)
    {
        $builder = new Builder();
        $builder
            ->setIssuedAt($this->clock->now())
            ->setExpiration($this->clock->plusTwentyMinutes())
            ->set('username', $username)
            ->sign(new Sha256(), $this->key);

        return $builder->getToken();
    }
}