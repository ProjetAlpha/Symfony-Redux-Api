<?php

namespace App\Security;

use App\Entity\User;
use Psr\Log\LoggerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

class TokenAuthenticator extends AbstractGuardAuthenticator
{
    private $em;

    private $session;

    private $encoder;

    private $logger;

    private $tokenProvider;

    public function __construct(EntityManagerInterface $em, SessionInterface $session, RouterInterface $router, UserPasswordEncoderInterface $encoder, LoggerInterface $logger)
    {
        $this->em = $em;
        $this->session = $session;
        $this->router = $router;
        $this->encoder = $encoder;
        $this->logger = $logger;
        $this->tokenProvider = new TokenProvider();
    }

    /**
     * Called on every request to decide if this authenticator should be
     * used for the request. Returning `false` will cause this authenticator
     * to be skipped.
     */
    public function supports(Request $request)
    {
        if ($_ENV['APP_ENV'] == 'dev') {
            $path = $request->getRequestUri();
            $matchAdminRoute = preg_match('#/api/admin/.*#', $path, $matches);
            
            $this->logger->info(' *** User Token Check *** ', ['token' => $request->headers->get('X-API-TOKEN') ]);
            $this->logger->info(' *** Match api user route ***', ['match' => $matches]);
        }

        if (!$request->headers->has('X-API-TOKEN')) {
            throw new UnauthorizedHttpException('WWW-Authenticate: Bearer realm="Api Token Missing"', 'Bad api token.');
        }

        return $request->headers->has('X-API-TOKEN');
    }

    /**
     * Called on every request. Return whatever credentials you want to
     * be passed to getUser() as $credentials.
     */
    public function getCredentials(Request $request)
    {
        return $request->headers->get('X-API-TOKEN');
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        if (null === $credentials) {
            // The token header was empty, authentication fails with HTTP Status
            // Code 401 "Unauthorized"
            throw new UnauthorizedHttpException('WWW-Authenticate: Bearer realm="Token Expired"', 'Wrong user credentials.');
        }
        
        $user = $this->em->getRepository(User::class)
        ->findOneBy(['apiToken' => $credentials]);

        if (!$user) {
            throw new UnauthorizedHttpException('WWW-Authenticate: Bearer realm="Token Expired"', 'Wrong user credentials.');
        }

        if (time() > $user->getExpireAtToken()) {
            $refreshAdmin = $this->em->getRepository(User::class)
            ->findOneBy(['refresh_token' => $credentials]) ?? null;

            if (!$refreshAdmin) {
                $this->tokenProvider->setToken(32);
                $token = $this->tokenProvider->getToken();

                $user->setRefreshToken($token);
    
                $this->em->persist($user);
                $this->em->flush();

                throw new UnauthorizedHttpException('WWW-Authenticate: Bearer realm="Token Expired"', 'Token expired, here is your refresh token.', null, 401, [
                    'refresh_token' => $token
                ]);
            }
        }

        return $user;
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

        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        $data = [
            // you may want to customize or obfuscate the message first
            'standardUserMessage' => strtr($exception->getMessageKey(), $exception->getMessageData()),

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
            'standardUserMessage' => 'Authentication Required',
        ];

        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }

    public function supportsRememberMe()
    {
        return false;
    }
}
