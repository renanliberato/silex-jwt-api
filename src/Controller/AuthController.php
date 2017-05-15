<?php
/**
 * Created by IntelliJ IDEA.
 * User: renan
 * Date: 29/4/2017
 * Time: 11:32 AM
 */

namespace App\Controller;

use App\Service\TokenService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthController
{
    /**
     * @var TokenService
     */
    private $tokenService;

    public function __construct(TokenService $tokenService)
    {
        $this->tokenService = $tokenService;
    }

    public function authenticate(Request $request)
    {
        $body = json_decode($request->getContent());

        if (empty($body)) {
            return new Response('', 401);
        }
    }
}