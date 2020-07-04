<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\User;
use App\Entity\Image;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class ApiController extends AbstractController
{
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

        if ($apiToken == null) {
            return new JsonResponse(['data' => 'bad request'], 404);
        }

        $user = $this->getDoctrine()
        ->getRepository(User::class)
        ->findOneBy(['apiToken' => $apiToken]);

        if ($user == null) {
            return new JsonResponse(['data' => 'bad request'], 404);
        }

        return new JsonResponse(['email' => $user->getEmail(), 'api_token' => $user->getApiToken()], 200);
    }

    /**
     * @Route("/api/image/upload", name="upload_image")
     */
    public function uploadImage(Request $request, ValidatorInterface $validator): Response
    {
        $imageData = $request->request->get('base64_image');
        $apiToken = $request->headers->get('X-AUTH-TOKEN');
        $name = $request->request->get('name');
        $extension = $request->request->get('extension');

        if (!$imageData || !$apiToken) {
            return new Response('Bad request.', Response::HTTP_BAD_REQUEST);
        }

        $entityManager = $this->getDoctrine()->getManager();
        $user = $entityManager
        ->getRepository(User::class)
        ->findOneBy(['apiToken' => $apiToken]);

        if (!$user) {
            return new Response('Bad request.', Response::HTTP_BAD_REQUEST);
        }

        $userId = $user->getId();
        $bin = base64_decode($imageData);
        $im = imageCreateFromString($bin);

        if (!$im) {
            return new Response('Internal error.', Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $userDirectory = $this->getParameter('upload_image_dir') . $userId;

        if (!is_dir($userDirectory)) {
            mkdir($userDirectory, 0777, true);
        }

        $destination = $userDirectory . '/' . $name . '-' . uniqid() . '.' . $extension;

        imagepng($im, $destination, 0);
        imagedestroy($im);

        $imageModel = new Image();
        $imageModel->setPath($destination);
        $imageModel->setName($name);

        $user->addImage($imageModel);

        // save image in database
        $entityManager->persist($imageModel);

        $entityManager->flush();

        return new Response('OK', Response::HTTP_OK);
    }

    /**
     * @Route("/api/image/search", name="search_image")
     */
    public function searchImages(Request $request, ValidatorInterface $validator): JsonResponse
    {
        $apiToken = $request->headers->get('X-AUTH-TOKEN');

        if (!$apiToken) {
            return new JsonResponse(['data' => 'bad request'], 404);
        }

        $entityManager = $this->getDoctrine()->getManager();
        $user = $entityManager
        ->getRepository(User::class)
        ->findOneBy(['apiToken' => $apiToken]);

        if (!$user) {
            return new JsonResponse(['data' => 'bad request'], 404);
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
}
