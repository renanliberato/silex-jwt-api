<?php
/**
 * Created by IntelliJ IDEA.
 * User: renan
 * Date: 29/4/2017
 * Time: 11:32 AM
 */

namespace App\Controller;

use App\DAO\UserDAO;
use App\Service\TokenService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthController
{
    /**
     * @var UserDAO
     */
    private $userDAO;

    /**
     * @var TokenService
     */
    private $tokenService;

    public function __construct(TokenService $tokenService, UserDAO $userDAO)
    {
        $this->userDAO    = $userDAO;
        $this->tokenService = $tokenService;
    }

    public function authenticate(Request $request)
    {
        $body = json_decode($request->getContent());

        if (empty($body)) {
            return new Response('', 401);
        }

        $username = $body->username;
        $password = $body->password;

        try {
            $userId = $this->userDAO->getByUsernameAndPassword($username, $password);

            if (!$userId) {
                return new Response('', 401);
            }

            return new Response('', 200);
        } catch (\Exception $e) {
            return new Response('', 401);
        }
    }
}