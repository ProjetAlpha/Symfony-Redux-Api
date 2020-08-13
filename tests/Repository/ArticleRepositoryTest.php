<?php

namespace App\Tests\Repository;

use App\Tests\UserHelper;

class ArticleRepositoryTest extends UserHelper
{
    /**
     * Specified an api token strategy
     *
     * @return void
     */
    public static function setUpBeforeClass(): void
    {
        static::$isAdmin = true;
    }
    
    /**
     * Test if a standard user can fetch a specified article.
     *
     *  @group article-repo
     *
     * @return void
     */
    public function testIfAUserCanFetchAnArticle()
    {
        $articleId = $this->createArticle();

        static::loginUser($this->client, $this->user->getEmail(), $this->originalUserPassword, 200);

        $this->client->request('GET', '/api/articles/'.$articleId);

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        static::assertJsonResponse(
            $this->client,
            [
            'raw_data' => $this->htmlSample,
            'id' => $articleId,
            'is_draft' => false,
            ]
        );
    }

    /**
     * Test if a standard user can fetch all articles.
     *
     *  @group article-repo
     *
     * @return void
     */
    public function testIfAUserCanFetchAllArticles()
    {
        $firstArticleId = $this->createArticle();
        $secondArticleId = $this->createArticle();

        static::loginUser($this->client, $this->user->getEmail(), $this->originalUserPassword, 200);

        $this->client->request('GET', '/api/articles/all');

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $json = static::assertJsonResponse($this->client);

        $this->assertCount(2, $json);
    }
}
