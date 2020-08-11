<?php

namespace App\Tests\Repository;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Tests\UserHelper;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AdminRepositoryTest extends WebTestCase
{
    /**
     * Html sample.
     *
     * @var string
     */
    protected $htmlSample;

    /**
     * Client request.
     *
     * @var
     */
    protected $client;

    /**
     * Entity manager.
     *
     * @var
     */
    protected $em;

    /**
     * Admin entity.
     *
     * @var
     */
    protected $admin;

    /**
     * Admin password.
     *
     * @var
     */
    protected $originalPassword;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        $this->em = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $encoder = $kernel->getContainer()->get('security.password_encoder');
        $this->makeAdmin($encoder);

        self::ensureKernelShutdown();
        $this->em->close();
        $this->em = null;
        $encoder = null;

        $this->client = static::createClient();
        $this->htmlSample = '<html><body><p>Hello World</p></body></html>';
    }

    /**
     * Create an admin test user.
     */
    private function makeAdmin($encoder): void
    {
        extract(UserHelper::createRandomUser());
        $this->originalPassword = $password;

        $user = new User();

        $user->setPassword($encoder->encodePassword($user, $password));
        $user->setEmail($email);
        $user->setFirstname($firstname);
        $user->setLastname($lastname);

        $user->setApiToken($apiToken);
        $user->setRoles(['ROLE_USER', 'ROLE_API_USER', 'ROLE_ADMIN']);
        $user->setIsAdmin(true);

        $this->admin = $user;
        $this->em->persist($user);
        $this->em->flush();
    }

    /**
     * Create a random admin article.
     *
     * @return int $articleId
     */
    private function createArticle($isDraft = false)
    {
        UserHelper::loginUser($this->client, $this->admin->getEmail(), $this->originalPassword);

        $adminId = $this->admin->getId();

        $this->client->request('POST', '/api/admin/'.$adminId.'/articles/create', [
            'is_draft' => $isDraft,
            'raw_data' => $this->htmlSample,
        ], [], []);

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $jsonResponse = UserHelper::assertJsonResponse($this->client, 'id');

        return $jsonResponse['id'];
    }

    /**
     * Test if a newly created admin user can access admin routes.
     *
     * @return void
     */
    public function testIfAnAdminUserIsAuthorized()
    {
        UserHelper::loginUser($this->client, $this->admin->getEmail(), $this->originalPassword);

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
     * @return void
     */
    public function testIfAnUnauthorizedUserIsDenied()
    {
        extract(UserHelper::createRandomUser());

        $this->client->request('POST', '/api/admin/me', ['email' => $email], [], []);

        $this->assertEquals(401, $this->client->getResponse()->getStatusCode());
    }

    /**
     * Test if a standard user can't access admin ressources.
     *
     * @return void
     */
    public function testIfAStandardUserIsUnauthorized()
    {
        extract(UserHelper::createRandomUser());

        UserHelper::registerUser($this->client, $email, $apiToken, $password, $firstname, $lastname);

        UserHelper::loginUser($this->client, $email, $password);

        $this->client->request('POST', '/api/admin/me', ['email' => $email], [], []);

        $this->assertEquals(403, $this->client->getResponse()->getStatusCode());
    }

    /**
     * Test if an administrator can find all standard users (none administrator users).
     *
     * @return void
     */
    public function testIfAnAdministratorCanFetchUsers()
    {
        extract(UserHelper::createRandomUser());

        // register a random user and log an administrator
        UserHelper::registerUser($this->client, $email, $apiToken, $password, $firstname, $lastname);

        UserHelper::loginUser($this->client, $this->admin->getEmail(), $this->originalPassword);

        $this->client->request('GET', '/api/admin/users/fetch', [], [], []);

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $response = $this->client->getResponse()->getContent();

        $this->assertJson($response);

        $data = json_decode($response, true);

        $hasNewUser = false;
        foreach ($data as $values) {
            $this->assertArrayHasKey('id', $values);
            $this->assertArrayHasKey('email', $values);
            $this->assertArrayHasKey('is_admin', $values);

            $this->assertEquals(null, $values['is_admin']);

            if ($values['email'] == $email) {
                $hasNewUser = true;
            }
        }

        $this->assertTrue($hasNewUser);
    }

    /**
     * Test if an administrator can delete a specified user.
     *
     * @return void
     */
    public function testIfAnAdministratorCanDeleteAUser()
    {
        extract(UserHelper::createRandomUser());

        // register a random user and log an administrator
        UserHelper::registerUser($this->client, $email, $apiToken, $password, $firstname, $lastname);

        UserHelper::loginUser($this->client, $this->admin->getEmail(), $this->originalPassword);

        $userRepository = static::$container->get(UserRepository::class);

        $user = $userRepository->findOneBy(['email' => $email]);

        $this->assertNotEmpty($user);

        $this->client->request('DELETE', '/api/admin/users/delete/'.$user->getId(),
            [
                'is_draft' => false, 'raw_data' => json_encode(['']), ], [], []);

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $userRepository = static::$container->get(UserRepository::class);

        $user = $userRepository->findOneBy(['email' => $email]);

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
        ]);

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        UserHelper::assertJsonResponse($this->client, 'id', $articleId);
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

        $this->client->request('GET', '/api/admin/'.$adminId.'/articles/'.$articleId, []);

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        UserHelper::assertJsonResponse($this->client, [
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

        $json = UserHelper::assertJsonResponse($this->client);

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

        $json = UserHelper::assertJsonResponse($this->client);

        $this->assertCount(1, $json);

        $article = $json[0];

        $this->assertArrayHasKey('is_draft', $article);
        $this->assertTrue($article['is_draft']);
    }
}
