<?php

namespace App\Tests\Repository;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Tests\UserHelper;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AdminRepositoryTest extends WebTestCase
{
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

        $this->client->request('DELETE', '/api/admin/users/delete/'.$user->getId(), [], [], []);

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $userRepository = static::$container->get(UserRepository::class);

        $user = $userRepository->findOneBy(['email' => $email]);

        $this->assertEmpty($user);
    }
}
