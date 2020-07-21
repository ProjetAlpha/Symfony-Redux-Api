<?php

namespace App\Controller;

use App\Entity\User;
use App\Security\UserSession;
use App\Services\Normalize;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RegistrationController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @var Normalize
     */
    private $normalize;

    public function __construct(EntityManagerInterface $entityManager, ValidatorInterface $validator, Normalize $normalize)
    {
        $this->entityManager = $entityManager;
        $this->validator = $validator;
        $this->normalize = $normalize;
    }

    /**
     * @Route("/", name="index")
     */
    public function index()
    {
        return $this->render('base.html.twig');
    }

    /**
     * @Route("/api/register", name="register")
     */
    public function register(Request $request, UserPasswordEncoderInterface $encoder): JsonResponse
    {
        $params = $request->request->all();

        $user = new User();

        $encoded = $encoder->encodePassword($user, $params['password']);
        $user->setPassword($encoded);
        $user->setEmail($params['email']);
        $user->setFirstname($params['firstname']);
        $user->setLastname($params['lastname']);

        $user->setApiToken($params['api_token'] ?? bin2hex(random_bytes(32)));
        $user->setRoles(['ROLE_USER', 'ROLE_API_USER']);

        // check if email is unique and if password and email are valid.
        $errors = $this->validator->validate($user);

        if (count($errors) > 0) {
            $messages = [];
            // normalize symfony validation.
            $messages = $this->normalize->transformSymfonyValidation($errors);

            return new JsonResponse($messages, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return new JsonResponse('Register Sucess!', Response::HTTP_CREATED);
    }

    /**
     * @Route("/api/login", name="login")
     */
    public function login(Request $request, ValidatorInterface $validator, UserPasswordEncoderInterface $encoder, SessionInterface $session): JsonResponse
    {
        $password = $request->request->get('password');
        $email = $request->request->get('email');

        if (!$password || !$email) {
            throw new BadRequestHttpException('Bad request input.');
        }

        $user = $this->entityManager
        ->getRepository(User::class)
        ->findOneBy(['email' => $email]);

        if (!$user) {
            throw new NotFoundHttpException('Wrong credentials, provide a valid username and password.');
        }

        if ($encoder->isPasswordValid($user, $password)) {
            $userSession = new UserSession();
            $userSession->setToken($user->getPassword())->setEmail($user->getEmail())->setId($user->getId());
            $userSession->setIsLoggedIn(true);

            // Token expire in 1 week.
            $userSession->setExpireAt(time() + (7 * 24 * 60 * 60));
            $session->set('userInfo', $userSession);

            return new JsonResponse([
                'email' => $user->getEmail(),
                'id' => $user->getId(),
                'firstname' => $user->getFirstname(),
                'lastname' => $user->getLastname(),
            ], Response::HTTP_OK);
        }

        return new JsonResponse(['error' => 'Wrong user login.'], Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @Route("/api/logout", name="logout")
     */
    public function logout(SessionInterface $session): Response
    {
        if ($session->get('userInfo')) {
            $session->invalidate();

            return new Response('Login OK.', Response::HTTP_OK);
        }
        $session->invalidate();

        return new Response('Unexpected logout request.', Response::HTTP_BAD_REQUEST);
    }

    /*
     * @Route("/api/profil", name="logout")
     */
    public function profil(Request $request, SessionInterface $session): JsonResponse
    {
        $id = $request->request->get('id');

        $user = $this->entityManager
        ->getRepository(User::class)
        ->findOneBy(['id' => $id]);

        if (!$user) {
            throw new NotFoundHttpException('Unexpected user.');
        }

        return  new JsonResponse([
            'image' => $user->getImages(),
            'lastname' => $user->getLastname(),
            'firstname' => $user->getFirstname(),
        ], Response::HTTP_OK);
    }
}
