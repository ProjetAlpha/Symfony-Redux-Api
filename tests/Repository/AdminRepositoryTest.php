<?php

namespace App\Tests\Repository;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Tests\UserHelper;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AdminRepositoryTest extends UserHelper
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
     * Test if a newly created admin user can access admin routes.
     *
     * @group admin-repo
     *
     * @return void
     */
    public function testIfAnAdminUserIsAuthorized()
    {
        static::loginUser($this->client, $this->admin->getEmail(), $this->originalAdminPassword);

        $this->client->request('POST', '/api/admin/me', ['email' => $this->admin->getEmail()], [], []);

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $response = $this->client->getResponse()->getContent();

        $this->assertJson($response);

        $this->assertEquals([
            'email' => $this->admin->getEmail(),
            'id' => $this->admin->getId(),
            'lastname' => $this->admin->getLastname(),
            'firstname' => $this->admin->getFirstname(),
        ], json_decode($response, true));
    }

    /**
     * Test if an unauthorized user has access denied.
     *
     * @group admin-repo
     *
     * @return void
     */
    public function testIfAnUnauthorizedUserIsDenied()
    {
        extract(UserHelper::createRandomUser());

        $this->client->request('POST', '/api/admin/me', ['email' => $email], [], []);

        $this->assertEquals(404, $this->client->getResponse()->getStatusCode());
    }

    /**
     * Test if a standard user can't access admin ressources.
     *
     * @group admin-repo
     *
     * @return void
     */
    public function testIfAStandardUserIsUnauthorized()
    {
        $this->client->request('POST', '/api/admin/me', ['email' => $this->user->getEmail()], [], []);

        $this->assertEquals(404, $this->client->getResponse()->getStatusCode());
    }

    /**
     * Test if an administrator can find all standard users (none administrator users).
     *
     * @group admin-repo
     *
     * @return void
     */
    public function testIfAnAdministratorCanFetchUsers()
    {
        $this->client->request('GET', '/api/admin/users/fetch', [], [], []);

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $response = $this->client->getResponse()->getContent();

        $this->assertJson($response);

        $data = json_decode($response, true);

        $hasNewUser = false;
        foreach ($data as $values) {
            if ($values['email'] == $this->user->getEmail()) {
                $this->assertArrayHasKey('id', $values);
                $this->assertArrayHasKey('email', $values);
                $this->assertArrayHasKey('is_admin', $values);

                $hasNewUser = true;
            }
        }

        $this->assertTrue($hasNewUser);
    }

    /**
     * Test if an administrator can delete a specified user.
     *
     * @group admin-repo
     *
     * @return void
     */
    public function testIfAnAdministratorCanDeleteAUser()
    {
        $userRepository = static::$container->get(UserRepository::class);

        $user = $userRepository->findOneBy(['email' => $this->admin->getEmail()]);

        $this->assertNotEmpty($user);

        $this->client->request(
            'DELETE',
            '/api/admin/users/delete/'.$user->getId(),
            [
                'is_draft' => false, 'raw_data' => json_encode(['']), ],
            [],
            []
        );

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $userRepository = static::$container->get(UserRepository::class);

        $user = $userRepository->findOneBy(['email' => $this->admin->getEmail()]);

        $this->assertEmpty($user);
    }

    /**
     * Test if an administrator user can published an article.
     *
     * @group admin-article
     *
     * @return void
     */
    public function testIfAnAdminCanCreateAnArticle()
    {
        $this->createArticle();
    }

    /**
     * Test if an administrator user can delete an article.
     *
     * @group admin-article
     *
     * @return void
     */
    public function testIfAnAdminCanDeleteAnArticle()
    {
        $adminId = $this->admin->getId();
        $articleId = $this->createArticle();

        $this->client->request('POST', '/api/admin/'.$adminId.'/articles/'.$articleId.'/delete');

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    /**
     * Test if an administrator user can update an article.
     *
     * @group admin-article
     *
     * @return void
     */
    public function testIfAnAdminCanUpdateAnArticle()
    {
        $adminId = $this->admin->getId();
        // create a draft article
        $articleId = $this->createArticle(true);

        $this->client->request('POST', '/api/admin/'.$adminId.'/articles/'.$articleId.'/update', [
            'is_draft' => false,
            'raw_data' => $this->htmlSample,
        ], []);

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        static::assertJsonResponse($this->client, 'id', $articleId);
    }

    /**
     * Test if an administrator can fetch a specified article.
     *
     * @group admin-article
     *
     * @return void
     */
    public function testIfAnAdminCanFetchAnArticle()
    {
        $adminId = $this->admin->getId();
        // create a draft article
        $articleId = $this->createArticle(true);

        $this->client->request('GET', '/api/admin/'.$adminId.'/articles/'.$articleId, [], []);

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        static::assertJsonResponse($this->client, [
        'user_id' => $adminId,
        'id' => $articleId,
        'raw_data' => $this->htmlSample,
        'is_draft' => true,
        ]);
    }

    /**
     *  Test if an administrator can fetch all his published articles.
     *
     * @group admin-article
     *
     * @return void
     */
    public function testIfAnAdminCanFetchAllHisPublishedArticles()
    {
        $adminId = $this->admin->getId();
        // create a published article
        $articleId = $this->createArticle();

        $this->client->request('POST', '/api/admin/'.$adminId.'/articles', ['is_draft' => false]);

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $json = static::assertJsonResponse($this->client);

        $this->assertCount(1, $json);

        $article = $json[0];

        $this->assertArrayHasKey('is_draft', $article);
        $this->assertFalse($article['is_draft']);
    }

    /**
     *  Test if an administrator can fetch all his draft articles.
     *
     * @group admin-article
     *
     * @return void
     */
    public function testIfAnAdminCanFetchAllHisDraftArticles()
    {
        $adminId = $this->admin->getId();
        // create a draft article
        $articleId = $this->createArticle(true);

        $this->client->request('POST', '/api/admin/'.$adminId.'/articles', ['is_draft' => true]);

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $json = static::assertJsonResponse($this->client);

        $this->assertCount(1, $json);

        $article = $json[0];

        $this->assertArrayHasKey('is_draft', $article);
        $this->assertTrue($article['is_draft']);
    }
}
