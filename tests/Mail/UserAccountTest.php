<?php

namespace App\Tests\Mail;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Tests\UserHelper;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserAccountTest extends WebTestCase
{
    /**
     * Client request.
     *
     * @var
     */
    protected $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
    }

    /**
     * Test if a logged in user should confirm is account.
     *
     * @group UserMail
     *
     * @return void
     */
    public function testIfAnAuthenticatedUserShouldConfirmIsAccount()
    {
        extract(UserHelper::createRandomUser());

        UserHelper::registerUser($this->client, $email, $apiToken, $password, $firstname, $lastname);

        UserHelper::loginUser($this->client, $email, $password);

        $userRepository = static::$container->get(UserRepository::class);

        $user = $userRepository->findOneBy(['email' => $email]);

        $this->assertNotEmpty($user->getConfirmationLink());

        $this->client->request('GET', '/api/register/confirmation/'.$user->getConfirmationLink(), [], [], []);

        $user = $userRepository->findOneBy(['email' => $email]);

        $this->assertEmpty($user->getConfirmationLink());

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    /**
     * Test if an unauthenticated user can send a reset password link.
     *
     * @group UserMail
     *
     * @return void
     */
    public function testIfAnUnauthenticatedUserCanSendAResetPassword()
    {
        extract(UserHelper::createRandomUser());

        UserHelper::registerUser($this->client, $email, $apiToken, $password, $firstname, $lastname);

        $this->client->request('POST', '/api/public/reset/send', ['email' => $email], [], []);

        $userRepository = static::$container->get(UserRepository::class);

        $user = $userRepository->findOneBy(['email' => $email]);

        $this->assertNotEmpty($user->getResetLink());

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    /**
     * Test if an unauthenticated user has access to his reset password link.
     *
     * @group UserMail
     *
     * @return void
     */
    public function testIfAnUnauthenticatedUserCanResetHisPassword()
    {
        extract(UserHelper::createRandomUser());

        UserHelper::registerUser($this->client, $email, $apiToken, $password, $firstname, $lastname);

        $this->client->request('POST', '/api/public/reset/send', ['email' => $email], [], []);

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $userRepository = static::$container->get(UserRepository::class);

        $user = $userRepository->findOneBy(['email' => $email]);

        $this->assertNotEmpty($user->getResetLink());

        $this->client->request('GET', '/api/public/reset/password/'.$user->getResetLink(), [], [], []);

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    /**
     * Test if an authenticated user can access to his reset link, reset his password
     * and if login works.
     *
     * @group UserMail
     *
     * @return void
     */
    public function testIfAnUnauthenticatedUserHasResetHisPassword()
    {
        extract(UserHelper::createRandomUser());

        UserHelper::registerUser($this->client, $email, $apiToken, $password, $firstname, $lastname);

        $this->client->request('POST', '/api/public/reset/send', ['email' => $email], [], []);

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $userRepository = static::$container->get(UserRepository::class);

        $user = $userRepository->findOneBy(['email' => $email]);
        $oldPassword = $user->getPassword();

        $this->assertNotEmpty($user->getResetLink());

        $this->client->request('GET', '/api/public/reset/password/'.$user->getResetLink(), [], [], []);

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $this->client->request('POST', '/api/public/reset/password/'.$user->getResetLink().'/confirm', ['password' => 'new123pwd'], [], []);

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $user = $userRepository->findOneBy(['email' => $email]);

        $this->assertNull($user->getResetLink());
        $this->assertNotEquals($user->getPassword(), $oldPassword);

        UserHelper::loginUser($this->client, $email, 'new123pwd');
    }
}
