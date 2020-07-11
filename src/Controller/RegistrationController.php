<?php

namespace App\Controller;

use App\Entity\User;
use App\Security\UserSession;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RegistrationController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index()
    {
        return $this->render('base.html.twig');
    }

    /**
     * @Route("/register", name="register")
     */
    public function register(GuardAuthenticatorHandler $guardHandler, Request $request, ValidatorInterface $validator, UserPasswordEncoderInterface $encoder): Response
    {
        $params = $request->request->all();

        $user = new User();

        $encoded = $encoder->encodePassword($user, $params['password']);
        $user->setPassword($encoded);
        $user->setEmail($params['email']);

        $user->setApiToken($params['api_token'] ?? bin2hex(random_bytes(32)));
        $user->setRoles(['ROLE_USER', 'ROLE_API_USER']);

        // check if email is unique and if password and email are valid.
        $errors = $validator->validate($user);

        if (count($errors) > 0) {
            $errorsString = (string) $errors;

            return new Response($errorsString, Response::HTTP_BAD_REQUEST);
        }

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($user);
        $entityManager->flush();

        return new Response('Register Sucess!', Response::HTTP_CREATED, ['content-type' => 'text/plain']);
    }

    /**
     * @Route("/login", name="login")
     */
    public function login(Request $request, ValidatorInterface $validator, UserPasswordEncoderInterface $encoder, SessionInterface $session): JsonResponse
    {
        $password = $request->request->get('password');
        $email = $request->request->get('email');

        $entityManager = $this->getDoctrine()->getManager();
        $user = $entityManager
        ->getRepository(User::class)
        ->findOneBy(['email' => $email]);

        if (!$user) {
            return new JsonResponse(['error' => 'Authentication error.'], Response::HTTP_BAD_REQUEST);
        }

        if ($encoder->isPasswordValid($user, $password)) {
            $userSession = new UserSession();
            $userSession->setToken($user->getPassword())->setEmail($user->getEmail())->setId($user->getId());
            $userSession->setIsLoggedIn(true);

            // Token expire in 1 week.
            $userSession->setExpireAt(time() + (7 * 24 * 60 * 60));
            $session->set('userInfo', $userSession);

            return new JsonResponse(['email' => $user->getEmail(), 'id' => $user->getId()], Response::HTTP_OK);
        }

        return new JsonResponse(['error' => 'Authentication error.'], Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @Route("/logout", name="logout")
     */
    public function logout(): Response
    {
        if (array_key_exists('userInfo', $_SESSION)) {
            unset($_SESSION['userInfo']);

            return new Response('Login OK.', Response::HTTP_OK);
        }

        return new Response('Unexpected logout request.', Response::HTTP_BAD_REQUEST);
    }
}
