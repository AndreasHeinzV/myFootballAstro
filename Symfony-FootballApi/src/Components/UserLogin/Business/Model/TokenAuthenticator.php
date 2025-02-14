<?php

declare(strict_types=1);

namespace App\Components\UserLogin\Business\Model;


use App\Components\User\Persistence\UserRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

class TokenAuthenticator extends AbstractAuthenticator
{
    public function __construct(private UserRepository $userRepository)
    {}

    public function supports(Request $request): ?bool
    {
        return $request->headers->has('token');
    }

    public function authenticate(Request $request): Passport
    {
        $token = $request->headers->get('token');
        if (null === $token) {
            throw new CustomUserMessageAuthenticationException('No API token provided');
        }

        $userIdentifier = $this->userRepository->getUserIdentifierByToken($token);

        return new SelfValidatingPassport(new UserBadge($userIdentifier));
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        $user = $token->getUser();
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        $data = [
            'message' => strtr($exception->getMessageKey(), $exception->getMessageData())
        ];

        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }
}