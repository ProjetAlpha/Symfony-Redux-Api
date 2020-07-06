<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\User;
use App\Security\UserSession;
use App\Security\LoginFormAuthenticator;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegistrationController extends AbstractController
{
    /**
     * @Route("/registration", name="registration")
     */
    public function index()
    {
        return $this->render('registration/index.html.twig', [
            'controller_name' => 'RegistrationController',
        ]);
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
    public function login(Request $request, ValidatorInterface $validator, UserPasswordEncoderInterface $encoder, SessionInterface $session) : Response
    {
        $password = $request->request->get('password');
        $email = $request->request->get('email');

        $entityManager = $this->getDoctrine()->getManager();
        $user = $entityManager
        ->getRepository(User::class)
        ->findOneBy(['email' => $email]);

        if (!$user) {
            return new Response ('Login error.', Response::HTTP_BAD_REQUEST);
        }

        if ($encoder->isPasswordValid($user, $password)) {

            $userSession = new UserSession();
            $userSession->setToken($user->getPassword())->setEmail($user->getEmail())->setId($user->getId());
            $userSession->setIsLoggedIn(true);
            
            // Token expire in 1 week.
            $userSession->setExpireAt(time() + (7 * 24 * 60 * 60));
            $session->set('userInfo', $userSession);
            return new Response ('Login OK.', Response::HTTP_OK);
        }

        return new Response('Authenticate error.', Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @Route("/logout", name="logout")
     */
    public function logout() : Response
    {
        if (array_key_exists('userInfo', $_SESSION)) {
            unset($_SESSION['userInfo']);
            return new Response ('Login OK.', Response::HTTP_OK);
        }

        return new Response ('Unexpected logout request.', Response::HTTP_BAD_REQUEST);
    }
}
