<?php

declare(strict_types=1);

namespace App\Components\UserRegister\Communication\Controller;

use App\Components\User\Persistence\UserEntityManager;
use App\Components\User\Persistence\UserMapper;
use App\Components\UserRegister\Business\Model\RegisterUser;
use App\Components\UserRegister\Communication\Form\RegisterForm;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class RegisterPageController extends AbstractController
{
    public function __construct(
        private readonly UserMapper $userMapper,
        private readonly UserEntityManager $userEntityManager,
        private readonly UserPasswordHasherInterface $userPasswordHasher,
        private RegisterUser $registerUser,
    ) {
    }


    #[Route('/register', name: 'register_page_post', methods: ['POST'])]
    public function register(Request $request): JsonResponse
    {
        {
            try {
                $content = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
            } catch (\JsonException $e) {
                return $this->json([
                    'status' => 'error',
                    'message' => 'Invalid JSON format',
                ], 400);
            }
          //  error_log("Raw request data: " . $content);
            if (empty($content['firstName']) || empty($content['lastName']) || empty($content['email']) || empty($content['password'])) {
                return $this->json([
                    'status' => 'error',
                    'message' => ' Missing required fields: firstName, lastName, email, or password',
                    ], 400);
            }


            $hashedPassword = $this->userPasswordHasher->hashPassword(new User(), $content['password']);

            $array = [
                'firstName' => $content['firstName'],
                'lastName' => $content['lastName'],
                'email' => $content['email'],
                'password' => $hashedPassword,
            ];

            $userDto = $this->userMapper->createUserDto($array);


            if ($this->registerUser->checkExistingUser($userDto)) {
                return $this->json([
                    'status' => 'error',
                    'message' => 'Email already registered',
                ], 409);  // HTTP 409 Conflict
            }


            $this->registerUser->saveUser($userDto);


            return $this->json([
                'status' => 'success',
                'message' => 'User successfully registered',
            ], Response::HTTP_CREATED); // HTTP 201 Created
        }
    }
}
