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
}
