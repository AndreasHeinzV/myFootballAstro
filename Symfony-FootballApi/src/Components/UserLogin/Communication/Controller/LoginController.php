<?php

namespace App\Components\UserLogin\Communication\Controller;

use App\Components\UserLogin\Business\Model\LoginUser;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginController extends AbstractController
{
    public function __construct(private LoginUser $loginUser)
    {
    }

    #[Route(path: '/login', name: 'app_login', methods: ['POST'])]
    public function login(#[CurrentUser] ?User $user): Response
    {
        if (null === $user) {
            return $this->json([
                'message' => 'Missing credentials',
            ], Response::HTTP_UNAUTHORIZED);
        }
        $token = bin2hex(random_bytes(32));

        $this->loginUser->updateLoginCredentials($user->getUserIdentifier(), $token);

        return $this->json([
            'user' => $user->getUserIdentifier(),
            'token' => $token,
        ], RESPONSE::HTTP_OK);
    }
}
