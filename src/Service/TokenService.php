<?php

namespace App\Service;

use App\Service\ClockInterface;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Hmac\Sha256;

class TokenService
{

    /**
     * Token builder
     *
     * @var Builder
     */
    private $builder;

    /**
     * Clock service
     *
     * @var ClockInterface
     */
    private $clock;

    /**
     * Token signer
     *
     * @var Sha256
     */
    private $signer;

    /**
     * Sign key
     *
     * @var string
     */
    private $key;

    public function __construct(ClockInterface $clock, $key)
    {
        $this->builder = new Builder();
        $this->signer = new Sha256();
        $this->clock = $clock;
        $this->key = $key;
    }

    public function getAccessToken($username)
    {
        $builder = new Builder();
        $builder
            ->setIssuedAt($this->clock->now())
            ->setExpiration($this->clock->plusFiveMinutes())
            ->set('username', $username);

        return $builder->getToken();
    }

    public function getRenewToken($username)
    {
        $builder = new Builder();
        $builder
            ->setIssuedAt($this->clock->now())
            ->setExpiration($this->clock->plusTwentyMinutes())
            ->set('username', $username);

        return $builder->getToken();
    }
}