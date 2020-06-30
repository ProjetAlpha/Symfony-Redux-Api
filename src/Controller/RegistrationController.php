<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\User;
use App\Security\LoginFormAuthenticator;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;

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
    public function register(GuardAuthenticatorHandler $guardHandler, Request $request, ValidatorInterface $validator): Response
    {
        $params = $request->request->all();

        $user = new User();
        
        $user->setPassword($params['password']);
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
        // after validating the user and saving them to the database
        // authenticate the user and use onAuthenticationSuccess on the authenticator
        /*return $guardHandler->authenticateUserAndHandleSuccess(
            $user,          // the User object you just created
            $request,
            $authenticator, // authenticator whose onAuthenticationSuccess you want to use
            'main'          // the name of your firewall in security.yaml
        );*/
    }
}
