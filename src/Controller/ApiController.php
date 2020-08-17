<?php

namespace App\Controller;

use App\Entity\Image;
use App\Entity\User;
use App\Traits\EmailMessage;
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

    public function __construct(EntityManagerInterface $entityManager, ValidatorInterface $validator, \Swift_Mailer $mailer)
    {
        $this->entityManager = $entityManager;
        $this->validator = $validator;
        $this->setMailer($mailer);
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
        $apiToken = $request->headers->get('X-API-TOKEN');

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
     * @Route("/api/public/token/refresh/{id}", name="refresh_token")
     */
    public function refreshToken(Request $request): JsonResponse
    {
        $token = $request->attributes->get('id');

        if (null == $token) {
            throw new BadRequestHttpException('Unexpected api token.');
        }

        $user = $this->entityManager
        ->getRepository(User::class)
        ->findOneBy(['refresh_token' => $token]);

        if (null == $user) {
            throw new NotFoundHttpException('Unexpected user api token.');
        }

        $newToken = bin2hex(random_bytes(32));
        $user->setApiToken($newToken);
        $user->setExpireAtToken(time() + 60 * 60);
        $user->setRefreshToken(null);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return new JsonResponse(['token' => $newToken], Response::HTTP_OK);
    }

    /**
     * @Route("/api/public/mail/send", name="send_mail")
     */
    public function sendMail(Request $request, \Swift_Mailer $mailer): JsonResponse
    {
        $email = $request->request->get('email');
        $type = $request->request->get('type');
        $messageId = $request->request->get('messageId');
        $subject = $request->request->get('subject');

        if (!$email || !$type || !$subject) {
            throw new BadRequestHttpException('Unexpected request input.');
        }

        $user = $this->entityManager
        ->getRepository(User::class)
        ->findOneBy(['email' => $email]);

        if (!$user || null !== $user->getResetLinkConfirmation()) {
            throw new NotFoundHttpException('Unexpected user.');
        }

        $link = bin2hex(random_bytes(32));

        // swift mailer use a custom smtp server
        $this->processMail(
            'noreply@universite-pub.site',
            $user->getEmail(),
            $subject,
            [
            'subject' => $subject,
            'user' => $user,
            'link' => $link,
            'message' => $this->getEmailMessage($type, $messageId),
            ],
            $type
        );

        return new JsonResponse(['message' => 'Mail successfully sent.'], Response::HTTP_OK);
    }

    /**
     * @Route("/api/image/upload", name="upload_image")
     */
    public function uploadImage(Request $request, ValidatorInterface $validator): JsonResponse
    {
        $imageData = $request->request->get('base64_image');
        $email = $request->request->get('email');
        $name = $request->request->get('name');
        $extension = $request->request->get('extension');
        $isArticleCover = $request->request->get('is_article_cover');

        if (!$imageData || !$email) {
            throw new BadRequestHttpException('Unexpected request input.');
        }

        $user = $this->entityManager->getRepository(User::class);

        $user = $user->findOneBy(['email' => $email]);

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
        $imageModel->setIsArticleCover($isArticleCover);

        $user->addImage($imageModel);

        // save image informations
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
        $email = $request->request->get('email');

        if (!$email) {
            throw new BadRequestHttpException('Unexpected request input.');
        }

        $user = $this->entityManager->getRepository(User::class);

        $user = $user->findOneBy(['email' => $email]);

        if (!$user) {
            throw new NotFoundHttpException('Unexpected user.');
        }

        $imgResult = [];
        foreach ($user->getImages() as $k => $image) {
            $imgResult[$k] = [
                'path' => $image->getPath(),
                'id' => $image->getId(),
                'name' => $image->getName(),
                'user_id' => $image->getUserId()->getId(),
                'is_article_cover' => $image->getIsArticleCover()
            ];
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
