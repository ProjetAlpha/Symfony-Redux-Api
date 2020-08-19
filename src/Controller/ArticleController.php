<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\User;
use App\Entity\Image;
use App\Services\Normalize;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ArticleController extends AbstractController
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
     * @Route("/api/admin/{admin_id}/articles", name="admin_article")
     */
    public function me(Request $request)
    {
        $userId = $request->attributes->get('admin_id');
        $isDraft = $request->request->get('is_draft');
        $apiToken = $request->headers->get('X-API-TOKEN');

        if (!$userId || !is_numeric($userId)) {
            throw new NotFoundHttpException('Unexpected article id.');
        }

        $user = $this->entityManager
        ->getRepository(User::class)
        ->findOneBy(['apiToken' => $apiToken]);

        if (!$user) {
            throw new NotFoundHttpException('Unexpected admin user.');
        }

        $result = [];
        foreach ($user->getArticles() as $article) {
            if ($isDraft && !$article->getIsDraft() || !$isDraft && $article->getIsDraft()) {
                continue;
            }

            $result[] = [
                'is_draft' => $article->getIsDraft(),
                'raw_data' => $article->getRawData(),
                'id' => $article->getId(),
                'user_id' => $article->getUserId()->getId(),
                'cover_id' => $article->getCoverId(),
                'title' => $article->getTitle(),
                'description' => $article->getDescription()
            ];
        }

        return new JsonResponse($result, Response::HTTP_OK);
    }

    /**
     * @Route("/api/articles/all", name="article_fetchAll")
     */
    public function all()
    {
        // all published articles, no draft
        $articles = $this->entityManager
        ->getRepository(Article::class)
        ->getAllPublished();

        return new JsonResponse($articles, Response::HTTP_OK);
    }

    /**
     * @Route("/api/admin/{admin_id}/articles/{article_id}", name="admin_article_fetch")
     */
    public function adminFetch(Request $request)
    {
        $userId = $request->attributes->get('admin_id');
        $articleId = $request->attributes->get('article_id');

        if (!$userId || !$articleId) {
            throw new NotFoundHttpException('Unexpected admin article fetch.');
        }

        $article = $this->entityManager
        ->getRepository(Article::class)
        ->findAdminArticle($userId, $articleId);

        if (!$article) {
            throw new NotFoundHttpException('No article found.');
        }

        return new JsonResponse([
            'is_draft' => $article->getIsDraft(),
            'raw_data' => $article->getRawData(),
            'title' => $article->getTitle(),
            'description' => $article->getDescription(),
            'id' => $article->getId(),
            'user_id' => $article->getUserId()->getId(),
            'cover_id' => $article->getCoverId()
        ], Response::HTTP_OK);
    }

    /**
     * @Route("/api/articles/{id}", name="article_fetch")
     */
    public function fetch(Request $request): JsonResponse
    {
        $id = $request->attributes->get('id');

        if (!$id || !is_numeric($id)) {
            throw new NotFoundHttpException('Unexpected article id.');
        }

        $article = $this->entityManager
        ->getRepository(Article::class)
        ->findOneBy(['id' => $id]);

        // draft articles are only accessible to admin user
        if (!$article || $article->getIsDraft()) {
            throw new NotFoundHttpException('Unexpected post request.');
        }

        return new JsonResponse([
            'raw_data' => $article->getRawData(),
            'is_draft' => $article->getIsDraft(),
            'title' => $article->getTitle(),
            'description' => $article->getDescription(),
            'id' => $article->getId(),
            'cover_id' => $article->getCoverId()
        ], Response::HTTP_OK);
    }

    /**
     * @Route("/api/admin/{admin_id}/articles/create", name="article_create")
     */
    public function create(Request $request)
    {
        $apiToken = $request->headers->get('X-API-TOKEN');

        $userId = $request->attributes->get('admin_id');
        $isDraft = $request->request->get('is_draft');
        $data = $request->request->get('raw_data');
        
        $title = $request->request->get('title');
        $description = $request->request->get('description');

        if (!$userId || !is_numeric($userId) || !$title) {
            throw new NotFoundHttpException('Unexpected article id.');
        }

        $user = $this->entityManager
        ->getRepository(User::class)
        ->findOneBy(['apiToken' => $apiToken]);

        if (!$user) {
            throw new NotFoundHttpException('Unexpected admin post request.');
        }

        $article = new Article();
        $article->setUserId($user);
        $article->setIsDraft($isDraft ?? false);
        $article->setRawData($data);
        $article->setTitle($title);
        $article->setDescription($description);

        $this->entityManager->persist($article);
        $this->entityManager->flush();

        return new JsonResponse(['id' => $article->getId(), 'isDraft' => $article->getIsDraft()], Response::HTTP_OK);
    }

    /**
     * @Route("/api/admin/{admin_id}/articles/{article_id}/update", name="article_update")
     */
    public function update(Request $request)
    {
        $apiToken = $request->headers->get('X-API-TOKEN');

        $userId = $request->attributes->get('admin_id');
        $articleId = $request->attributes->get('article_id');

        $isDraft = $request->request->get('is_draft') ?? false;
        $rawData = $request->request->get('raw_data');
        
        $title = $request->request->get('title');
        $description = $request->request->get('description');

        if (!$userId || !$articleId || !$title || !$description) {
            throw new NotFoundHttpException('Unexpected admin article update');
        }

        $user = $this->entityManager
        ->getRepository(User::class)
        ->findOneBy(['apiToken' => $apiToken]);

        if (!$user) {
            throw new NotFoundHttpException('Unexpected admin post request.');
        }

        $article = $this->entityManager
        ->getRepository(Article::class)
        ->findOneBy(['id' => $articleId]);

        if (!$article) {
            throw new NotFoundHttpException('Unexpected admin post request.');
        }

        $article->setRawData($rawData);
        $article->setTitle($title);
        $article->setDescription($description);
        $article->setIsDraft($isDraft);

        $this->entityManager->persist($article);
        $this->entityManager->flush();

        return new JsonResponse(['id' => $articleId], Response::HTTP_OK);
    }

    /**
     * @Route("/api/admin/{admin_id}/articles/{article_id}/delete", name="article_update")
     */
    public function delete(Request $request)
    {
        $userId = $request->attributes->get('admin_id');
        $coverId = $request->request->get('cover_id');
        $articleId = (int)$request->attributes->get('article_id');
        $apiToken = $request->headers->get('X-API-TOKEN');

        if (!$userId || !$articleId) {
            throw new NotFoundHttpException('Unexpected admin article delete.');
        }

        $user = $this->entityManager
        ->getRepository(User::class)
        ->findOneBy(['apiToken' => $apiToken]);

        if (!$user) {
            throw new NotFoundHttpException('Unexpected admin post request.');
        }

        $article = $this->entityManager
        ->getRepository(Article::class)
        ->findOneBy(['id' => $articleId]);

        if (!$article) {
            throw new NotFoundHttpException('Unexpected article id request.');
        }

        $this->entityManager->remove($article);
        $this->entityManager->flush();

        if ($coverId) {
            $image = $this->entityManager
            ->getRepository(Image::class)
            ->findOneBy(['id' => $coverId]);

            if ($image) {
                if (file_exists($image->getPath())) {
                    unlink($image->getPath());
                }
                $this->entityManager->remove($image);
                $this->entityManager->flush();
            }
        }

        return new JsonResponse(['id' => $articleId], Response::HTTP_OK);
    }
}
