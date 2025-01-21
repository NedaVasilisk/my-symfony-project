<?php

namespace App\EventListener;

use App\Entity\User;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\Cookie;

class AuthenticationSuccessListener
{
    private JWTTokenManagerInterface $jwtManager;

    public function __construct(JWTTokenManagerInterface $jwtManager)
    {
        $this->jwtManager = $jwtManager;
    }

    #[AsEventListener(event: 'lexik_jwt_authentication.on_authentication_success')]
    public function onAuthenticationSuccess(AuthenticationSuccessEvent $event): void
    {
        $user = $event->getUser();

        if (!$user instanceof User) {
            return;
        }

        $refreshToken = $this->jwtManager->createFromPayload($user, [
            'type' => 'refresh',
            'exp' => time() + 3600 * 24 * 7,
        ]);

        $data = $event->getData();
        $data['refresh_token'] = $refreshToken;
        $event->setData($data);

        $response = $event->getResponse();
        $response->headers->setCookie(
            Cookie::create('REFRESH_TOKEN', $refreshToken, time() + 3600 * 24 * 7, '/', null, true, true, false, 'Strict')
        );
    }
}
