<?php

namespace App\Controller;

use App\Entity\Image;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ApiController extends AbstractController
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
     * @Route("/api", name="api")
     */
    public function index()
    {
        return $this->render('api/index.html.twig', [
            'controller_name' => 'ApiController',
        ]);
    }

    /**
     * @Route("/api/me", name="me")
     */
    public function me(Request $request): JsonResponse
    {
        $apiToken = $request->headers->get('X-AUTH-TOKEN');

        if (null == $apiToken) {
            throw new BadRequestHttpException('Unexpected api token.');
        }

        $user = $this->entityManager
        ->getRepository(User::class)
        ->findOneBy(['apiToken' => $apiToken]);

        if (null == $user) {
            throw new NotFoundHttpException('Unexpected user api token.');
        }

        return new JsonResponse(['email' => $user->getEmail(), 'api_token' => $user->getApiToken()], 200);
    }

    /**
     * @Route("/api/image/upload", name="upload_image")
     */
    public function uploadImage(Request $request, ValidatorInterface $validator): JsonResponse
    {
        $imageData = $request->request->get('base64_image');
        $email = $request->request->get('email');
        $apiToken = $request->headers->get('X-AUTH-TOKEN');
        $name = $request->request->get('name');
        $extension = $request->request->get('extension');

        if (!$imageData || !$apiToken && !$email) {
            throw new BadRequestHttpException('Unexpected request input.');
        }

        $user = $this->entityManager->getRepository(User::class);

        $user = $apiToken ? $user->findOneBy(['apiToken' => $apiToken]) : $user->findOneBy(['email' => $email]);

        if (!$user) {
            throw new NotFoundHttpException('Unexpected user.');
        }

        $userId = $user->getId();
        $bin = base64_decode($imageData);
        $im = imagecreatefromstring($bin);

        if (!$im) {
            throw new ServiceUnavailableHttpException('Image format internal error.');
        }

        $userDirectory = $this->getParameter('upload_image_dir').$userId;

        if (!is_dir($userDirectory)) {
            mkdir($userDirectory, 0777, true);
        }

        $destination = $userDirectory.'/'.$name.'-'.uniqid().'.'.$extension;

        imagepng($im, $destination, 0);
        imagedestroy($im);

        $imageModel = new Image();
        $imageModel->setPath($destination);
        $imageModel->setName($name);

        $user->addImage($imageModel);

        // save image in database
        $this->entityManager->persist($imageModel);

        $this->entityManager->flush();

        return new JsonResponse([
            'name' => $imageModel->getName(),
            'path' => $imageModel->getPath(),
            'id' => $imageModel->getId(),
        ], Response::HTTP_OK);
    }

    /**
     * @Route("/api/image/search", name="search_image")
     */
    public function searchImages(Request $request, ValidatorInterface $validator): JsonResponse
    {
        $apiToken = $request->headers->get('X-AUTH-TOKEN');
        $email = $request->request->get('email');

        if (!$apiToken && !$email) {
            throw new BadRequestHttpException('Unexpected request input.');
        }

        $user = $this->entityManager->getRepository(User::class);

        $user = $apiToken ? $user->findOneBy(['apiToken' => $apiToken]) : $user->findOneBy(['email' => $email]);

        if (!$user) {
            throw new NotFoundHttpException('Unexpected user.');
        }

        $imgResult = [];
        foreach ($user->getImages() as $k => $image) {
            $imgResult[$k]['path'] = $image->getPath();
            $imgResult[$k]['id'] = $image->getId();
            $imgResult[$k]['name'] = $image->getName();
            $imgResult[$k]['user_id'] = $image->getUserId()->getId();
        }

        return new JsonResponse(['email' => $user->getEmail(), 'images' => $imgResult, 'id' => $user->getId()], 200);
    }

    /**
     * @Route("/api/image/delete", name="delete_image")
     */
    public function deleteImage(Request $request, ValidatorInterface $validator): JsonResponse
    {
        $email = $request->request->get('email');
        $imageId = $request->request->get('img_id');

        if (!$email) {
            throw new BadRequestHttpException('Bad request input.');
        }

        $user = $this->entityManager
        ->getRepository(User::class)
        ->findOneBy(['email' => $email]);

        if (!$user) {
            throw new NotFoundHttpException('Unexpected user.');
        }

        $deletedImage = [];
        $i = 0;
        if (is_array($imageId)) {
            foreach ($imageId as $id) {
                foreach ($user->getImages() as $image) {
                    if ($image->getId() == $id) {
                        $user->removeImage($image);
                        $this->entityManager->remove($image);
                        if (file_exists($image->getPath())) {
                            unlink($image->getPath());
                        }
                        $deletedImage[$i]['path'] = $image->getPath();
                        $deletedImage[$i]['name'] = $image->getName();
                        $deletedImage[$i]['id'] = $image->getId();
                        ++$i;
                    }
                }
            }
        } else {
            /*
                $image = $entityManager
                ->getRepository(User::class)
                ->findOneBy(['id' => $imageId]);
                $user->removeImage($image);
                $entityManager->remove($image);
                if (file_exists($image->getPath())) unlink($image->getPath());
            */
            foreach ($user->getImages() as $image) {
                if ($imageId == $image->getId()) {
                    $user->removeImage($image);
                    $this->entityManager->remove($image);
                    if (file_exists($image->getPath())) {
                        unlink($image->getPath());
                    }
                    $deletedImage[$i]['path'] = $image->getPath();
                    $deletedImage[$i]['name'] = $image->getName();
                    $deletedImage[$i]['id'] = $image->getId();
                    ++$i;
                }
            }
        }

        $this->entityManager->flush();

        return new JsonResponse($deletedImage, Response::HTTP_OK);
    }
}
