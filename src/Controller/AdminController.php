<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AdminController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var ValidatorInterface
     */
    private $validator;

    public function __construct(EntityManagerInterface $entityManager, ValidatorInterface $validator)
    {
        $this->entityManager = $entityManager;
        $this->validator = $validator;
    }

    /**
     * @Route("/api/admin/me", name="admin")
     */
    public function me(Request $request): JsonResponse
    {
        $email = $request->request->get('email');

        if (!$email) {
            throw new NotFoundHttpException('Unexpected admin user.');
        }

        $admin = $this->entityManager
        ->getRepository(User::class)
        ->findOneBy(['email' => $email]);

        if (!$admin || !$admin->getIsAdmin()) {
            throw new NotFoundHttpException('Unexpected admin user.');
        }

        return new JsonResponse(
            [
                'id' => $admin->getId(),
                'email' => $admin->getEmail(),
                'lastname' => $admin->getLastname(),
                'firstname' => $admin->getFirstname(),
            ],
            Response::HTTP_OK
        );
    }

    /**
     * @Route("/api/admin/users/fetch", name="admin_fetch_user")
     */
    public function getUsers()
    {
        $users = $this->entityManager
        ->getRepository(User::class)
        ->getUsers(false);

        return new JsonResponse($users, Response::HTTP_OK);
    }

    /**
     * @Route("/api/admin/users/delete/{id}", name="admin_delete_user")
     */
    public function removeUser(Request $request)
    {
        $id = $request->attributes->get('id');

        if (!$id) {
            throw new NotFoundHttpException('Unexpected admin post.');
        }

        $user = $this->entityManager
        ->getRepository(User::class)
        ->findOneBy(['id' => $id]);

        if (!$user) {
            throw new NotFoundHttpException('Unexpected admin post.');
        }

        $response = new JsonResponse([
            'email' => $user->getEmail(),
            'firstname' => $user->getLastname(),
            'lastname' => $user->getFirstname(),
            'id' => $user->getId(),
        ], Response::HTTP_OK);

        $this->entityManager->remove($user);
        $this->entityManager->flush();

        return $response;
    }
}
