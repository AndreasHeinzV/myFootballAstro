<?php

declare(strict_types=1);

namespace App\Components\UserLogin\Communication\Controller;

use App\Components\UserLogin\Business\Model\LogoutUser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class LogoutController extends AbstractController
{
    public function __construct(private readonly LogoutUser $logoutUser)
    {

    }
    #[Route(path: '/logout', name: 'app_logout', methods: ['POST'])]
    public function logout(Request $request): JsonResponse
    {
        $decoded = json_decode($request->getContent(), true,512, JSON_THROW_ON_ERROR);

        $content = $decoded['token'];
        //$content = $request->request->get('token');

       $status =  $this->logoutUser->logoutUser($content);


        if ($status) {
            return new JsonResponse([
                'status' => 'success',
                'message' => 'You have been logged out.',
            ], Response::HTTP_OK);
        }
        return new JsonResponse([
            'status' => 'error',
            'message' => 'Logout failed.',
        ]  , Response::HTTP_BAD_REQUEST);
    }
}
