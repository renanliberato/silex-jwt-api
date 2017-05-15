<?php

namespace App\Service;

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

    public function __construct($key)
    {
        $this->builder = new Builder();
        $this->signer = new Sha256();
        $this->key = $key;
    }

    public function getAccessToken($email)
    {
        $builder = new Builder();
        $builder
            ->setIssuedAt(strtotime('now'))
            ->setExpiration(strtotime('+5 minutes'))
            ->set('email', $email);

        return $builder->getToken();
    }

    public function getRenewToken($email)
    {
        $builder = new Builder();
        $builder
            ->setIssuedAt(strtotime('now'))
            ->setExpiration(strtotime('+20 minutes'))
            ->set('email', $email);

        return $builder->getToken();
    }
}