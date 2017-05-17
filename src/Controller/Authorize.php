<?php

namespace App\Controller;

use Exception;
use App\Service\TokenService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Authorize
{
    /**
     * @var TokenService
     */
    private $tokenService;

    public function __construct(TokenService $tokenService)
    {
        $this->tokenService = $tokenService;
    }

    public function process(Request $request)
    {
        if ($request->getPathInfo() == '/auth') {
            return;
        }
        try {
            $token = $this->tokenService->getFromRequest($request);
            
            if (!$this->tokenService->isAuthorized($token)) {
                return new Response('', 401);
            }
        } catch (Exception $e) {
            return new Response('', 401);
        }
    }
}