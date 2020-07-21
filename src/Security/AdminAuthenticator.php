<?php

namespace App\Security;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

class AdminAuthenticator extends AbstractGuardAuthenticator
{
    private $em;

    private $session;

    private $encoder;

    public function __construct(EntityManagerInterface $em, SessionInterface $session, RouterInterface $router, UserPasswordEncoderInterface $encoder)
    {
        $this->em = $em;
        $this->session = $session;
        $this->router = $router;
        $this->encoder = $encoder;
    }

    /**
     * Called on every request to decide if this authenticator should be
     * used for the request. Returning `false` will cause this authenticator
     * to be skipped.
     */
    public function supports(Request $request)
    {
        return $this->session->get('userInfo');
    }

    /**
     * Called on every request. Return whatever credentials you want to
     * be passed to getUser() as $credentials.
     */
    public function getCredentials(Request $request)
    {
        return [
            'userInfo' => $this->session->get('userInfo'),
        ];
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        if (null === $credentials) {
            // The token header was empty, authentication fails with HTTP Status
            // Code 401 "Unauthorized"
            throw new AccessDeniedHttpException('Wrong admin credentials..');
        }

        $admin = $this->em->getRepository(User::class)
        ->findOneBy(['email' => $credentials['userInfo']->getEmail()]);

        if (!$admin) {
            throw new AccessDeniedHttpException('Wrong admin credentials.');
        }

        if (!$admin->getIsAdmin()) {
            throw new AccessDeniedHttpException('Wrong access level.');
        }

        $isAuth = hash_equals($admin->getPassword(), $credentials['userInfo']->getToken());

        if (!$isAuth) {
            throw new AccessDeniedHttpException('Wrong admin credentials.');
        }

        return $admin;
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        // Check credentials - e.g. make sure the password is valid.
        // In case of an API token, no credential check is needed.

        return true;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        // on success, let the request continue
        // log user access, monitoring... ?

        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        $data = [
            // you may want to customize or obfuscate the message first
            'adminUserMessage' => strtr($exception->getMessageKey(), $exception->getMessageData()),

            // or to translate this message
            // $this->translator->trans($exception->getMessageKey(), $exception->getMessageData())
        ];

        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }

    /**
     * Called when authentication is needed, but it's not sent.
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
        $data = [
            // you might translate this message
            'adminUserMessage' => 'Authentication Required',
        ];

        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }

    public function supportsRememberMe()
    {
        return false;
    }
}
