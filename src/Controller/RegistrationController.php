<?php

namespace App\Controller;

use App\Entity\User;
use App\Security\UserSession;
use App\Services\Normalize;
use App\Traits\EmailMessage;
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
    /*
     * Api email message manager.
     */
    use EmailMessage;

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

    public function __construct(EntityManagerInterface $entityManager, ValidatorInterface $validator, Normalize $normalize, \Swift_Mailer $mailer)
    {
        $this->entityManager = $entityManager;
        $this->validator = $validator;
        $this->normalize = $normalize;
        $this->setMailer($mailer);
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

        $link = bin2hex(random_bytes(32));

        $user->setConfirmationLink($link);
        $user->setConfirmationLinkTimeout(time() + 24 * 60 * 60);

        // check if email is unique and if password and email are valid.
        $errors = $this->validator->validate($user);

        if (count($errors) > 0) {
            $messages = [];
            // normalize symfony validation.
            $messages = $this->normalize->transformSymfonyValidation($errors);

            return new JsonResponse($messages, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $subject = "Confirmation de la création d'un nouveau compte";
        $type = 'link';
        $mailTarget = $this->getEmailUrl($link, '/register/confirmation');

        $this->processMail(
            'gmonacho@universite-pub.site',
            $user->getEmail(),
            $subject,
            [
            'subject' => $subject,
            'user' => $user,
            'link' => $mailTarget,
            'message' => $this->getEmailMessage($type, 'confirmation'),
            ],
            $type
        );

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return new JsonResponse([
            'confirmationLink' => $link,
        ], Response::HTTP_CREATED);
    }

    /**
     * @Route("/api/register/confirmation/{id}", name="account_confirmation")
     */
    public function confirmAccount(Request $request): JsonResponse
    {
        $param = $request->attributes->get('id');

        if (!isset($param)) {
            throw new BadRequestHttpException('Bad request input.');
        }

        $user = $this->entityManager
        ->getRepository(User::class)
        ->findOneBy(['confirmation_link' => $param]);

        if (!$user || null === $user->getConfirmationLinkTimeout()) {
            throw new NotFoundHttpException('Unexpected user.');
        }

        if (time() > $user->getConfirmationLinkTimeout()) {
            return new JsonResponse(['error' => 'This confirmation link is expired.'], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $user->setConfirmationLink(null);
        $user->setConfirmationLinkTimeout(null);
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return new JsonResponse('Your account is now activated.', Response::HTTP_OK);
    }

    /**
     * @Route("/api/public/reset/password/{id}", name="reset_password_link")
     */
    public function resetPasswordLink(Request $request)
    {
        $param = $request->attributes->get('id');

        if (!isset($param)) {
            throw new BadRequestHttpException('Bad request input.');
        }

        $user = $this->entityManager
        ->getRepository(User::class)
        ->findOneBy(['reset_link' => $param]);

        if (!$user || null === $user->getResetLinkTimeout()) {
            throw new NotFoundHttpException('Reset link is expired.');
        }

        if (time() > $user->getResetLinkTimeout()) {
            return new JsonResponse(['error' => 'Reset link is expired.'], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return new JsonResponse(['link' => $user->getResetLink()], Response::HTTP_OK);
    }

    /**
     * @Route("/api/public/reset/send", name="send_reset_password_link")
     */
    public function sendResetPasswordLink(Request $request, \Swift_Mailer $mailer): JsonResponse
    {
        $email = $request->request->get('email');

        if (!$email) {
            throw new BadRequestHttpException('Unexpected request input.');
        }

        $user = $this->entityManager
        ->getRepository(User::class)
        ->findOneBy(['email' => $email]);

        // TODO : security.
        if (!$user) {
            throw new NotFoundHttpException('Unexpected user.');
        }

        $link = bin2hex(random_bytes(32));
        $mailTarget = $this->getEmailUrl($link, '/resetPassword/link/');
        $subject = 'Réinitialiser le mot de passe';
        $type = 'link';

        $this->processMail(
            'gmonacho@universite-pub.site',
            $user->getEmail(),
            $subject,
            [
            'subject' => $subject,
            'user' => $user,
            'link' => $mailTarget,
            'message' => $this->getEmailMessage($type, 'reset'),
            ],
            $type
        );

        $user->setResetLink($link);
        $user->setResetLinkTimeout(time() + 24 * 60 * 60);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return new JsonResponse(['message' => 'Mail successfully sent.'], Response::HTTP_OK);
    }

    /**
     * @Route("/api/public/reset/password/{id}/confirm", name="reset_password")
     */
    public function resetPassword(Request $request, UserPasswordEncoderInterface $encoder)
    {
        $linkId = $request->attributes->get('id');
        $password = $request->request->get('password');

        if (!$linkId || !$password) {
            throw new BadRequestHttpException('Bad request input.');
        }

        $user = $this->entityManager
        ->getRepository(User::class)
        ->findOneBy(['reset_link' => $linkId]);

        if (!$user || null === $user->getResetLinkTimeout()) {
            throw new NotFoundHttpException('Unexpected user.');
        }

        if (time() > $user->getResetLinkTimeout()) {
            return new JsonResponse(['error' => 'This reset link is expired.'], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $newPassword = $encoder->encodePassword($user, $password);
        $user->setPassword($newPassword);

        $user->setResetLink(null);
        $user->setResetLinkTimeout(null);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return new JsonResponse(['message' => 'Password reset success.'], Response::HTTP_OK);
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
            $userSession->setIsAdmin($user->getIsAdmin());

            // Token expire in 1 week.
            $userSession->setExpireAt(time() + (7 * 24 * 60 * 60));
            $session->set('userInfo', $userSession);

            return new JsonResponse([
                'email' => $user->getEmail(),
                'id' => $user->getId(),
                'firstname' => $user->getFirstname(),
                'lastname' => $user->getLastname(),
                'isAdmin' => $user->getIsAdmin(),
                'isConfirmed' => null === $user->getConfirmationLink(),
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
