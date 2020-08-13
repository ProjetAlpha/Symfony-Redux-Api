<?php

namespace App\Tests\Mail;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Tests\UserHelper;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserAccountTest extends UserHelper
{
    /**
     * Test if a logged in user should confirm is account.
     *
     * @group UserMail
     *
     * @return void
     */
    public function testIfAnAuthenticatedUserShouldConfirmIsAccount()
    {
        extract(static::createRandomUser());
        static::registerUser($this->client, $email, $apiToken, $password, $firstname, $lastname);
        static::loginUser($this->client, $email, $password);

        $userRepository = static::$container->get(UserRepository::class);

        $user = $userRepository->findOneBy(['email' => $email]);

        $this->assertNotEmpty($user->getConfirmationLink());

        $this->client->request('GET', '/api/register/confirmation/'.$user->getConfirmationLink(), [], [], ['HTTP_X-API-TOKEN' => $apiToken]);

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
        static::loginUser($this->client, $this->user->getEmail(), $this->originalUserPassword);

        $this->client->request('POST', '/api/public/reset/send', ['email' => $this->user->getEmail()], [], []);

        $userRepository = static::$container->get(UserRepository::class);

        $user = $userRepository->findOneBy(['email' => $this->user->getEmail()]);

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
        static::loginUser($this->client, $this->user->getEmail(), $this->originalUserPassword);

        $this->client->request('POST', '/api/public/reset/send', ['email' => $this->user->getEmail()], [], []);

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $userRepository = static::$container->get(UserRepository::class);

        $user = $userRepository->findOneBy(['email' => $this->user->getEmail()]);

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
        static::loginUser($this->client, $this->user->getEmail(), $this->originalUserPassword);

        $this->client->request('POST', '/api/public/reset/send', ['email' => $this->user->getEmail()], [], []);

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        // config/packages/test/web_profiler.yaml - profiler: { enabled: true, collect: true }
        // $mailCollector = $this->client->getProfile()->getCollector('swiftmailer');

        // checks that an email was sent
        // $this->assertSame(1, $mailCollector->getMessageCount());

        $userRepository = static::$container->get(UserRepository::class);

        $user = $userRepository->findOneBy(['email' => $this->user->getEmail()]);
        $oldPassword = $user->getPassword();

        $this->assertNotEmpty($user->getResetLink());

        $this->client->request('GET', '/api/public/reset/password/'.$user->getResetLink(), [], [], []);

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $this->client->request('POST', '/api/public/reset/password/'.$user->getResetLink().'/confirm', ['password' => 'new123pwd'], [], []);

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $user = $userRepository->findOneBy(['email' => $this->user->getEmail()]);

        $this->assertNull($user->getResetLink());
        $this->assertNotEquals($user->getPassword(), $oldPassword);

        static::loginUser($this->client, $this->user->getEmail(), 'new123pwd');
    }
}
