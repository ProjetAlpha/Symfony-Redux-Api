<?php

namespace App\Tests\Routes;

use App\Repository\UserRepository;
use App\Tests\UserHelper;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PublicRouteTest extends WebTestCase
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
     * Test if a user can reset his password without been logged in.
     *
     * @group routes
     *
     * @return void
     */
    public function testIfAnAnonymousUserCanResetHisPassword()
    {
        extract(UserHelper::createRandomUser());

        UserHelper::registerUser($this->client, $email, $apiToken, $password, $firstname, $lastname);

        $this->client->request('POST', '/api/public/reset/send', ['email' => $email], [], []);

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $userRepository = static::$container->get(UserRepository::class);

        $user = $userRepository->findOneBy(['email' => $email]);

        $this->client->request('GET', '/api/public/reset/password/'.$user->getResetLink(), [], [], []);

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }
}
