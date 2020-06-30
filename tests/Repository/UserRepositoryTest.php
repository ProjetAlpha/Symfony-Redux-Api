<?php

namespace App\Tests\Repository;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserRepositoryTest extends WebTestCase
{

    public function testUnauthoriziedApiUser()
    {
        $client = static::createClient();

        // create a random user
        extract($this->createRandomUser());

        // find api user token
        $client->request('POST', '/api/me/', [], [], ['HTTP_X-AUTH-TOKEN' => $apiToken]);

        // check unauthorizied response status
        $this->assertEquals(401, $client->getResponse()->getStatusCode());
    }

    public function testAuthoriziedApiUser()
    {
        $client = static::createClient();

        // create a random user
        extract($this->createRandomUser());

        $client->request(
            'POST',
            '/register',
            [
                'api_token' => $apiToken,
                'email' => $email,
                'password' => $password,
            ]
        );

        $this->assertEquals(201, $client->getResponse()->getStatusCode());

        // find api user token
        $client->request('POST', '/api/me/', [], [], ['HTTP_X-AUTH-TOKEN' => $apiToken]);

        // check response status
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        
        // check json response
        $this->assertEquals('application/json', $client->getResponse()->headers->get('content-type'));

        $data = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('api_token', $data);
        $this->assertEquals($email, $data['email']);
    }

    public function testBasicUserRegister()
    {
        $client = static::createClient();

        extract($this->createRandomUser());

        $client->request(
            'POST',
            '/register',
            [
                'api_token' => $apiToken,
                'email' => $email,
                'password' => $password,
            ]
        );
        
        $this->assertEquals(201, $client->getResponse()->getStatusCode());
    }

    public function testIfUserHasUniqueEmail()
    {
        $client = static::createClient();

        extract($this->createRandomUser());

        $client->request(
            'POST',
            '/register',
            [
                'api_token' => $apiToken,
                'email' =>  $email,
                'password' => $password,
            ]
        );

        // second request with same credentials
        $client->request(
            'POST',
            '/register',
            [
                'api_token' => $apiToken,
                'email' => $email,
                'password' => $password,
            ]
        );

        $this->assertEquals(400, $client->getResponse()->getStatusCode());
    }

    public function testJsonBodyRequestRegister()
    {
        $client = static::createClient();

        $client->request('POST', '/register', [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_X-Requested-With' => 'XMLHttpRequest'
        ], json_encode($this->createRandomUser()));

        $this->assertEquals(201, $client->getResponse()->getStatusCode());
    }

    public function testJsonParametersRequestRegister()
    {
        $client = static::createClient();

        $client->request('POST', '/register', $this->createRandomUser(), [], [
            'HTTP_X-Requested-With' => 'XMLHttpRequest'
        ]);

        $this->assertEquals(201, $client->getResponse()->getStatusCode());
    }

    private function createRandomUser() : array
    {
        $apiToken = bin2hex(random_bytes(32));
        $password = bin2hex(random_bytes(32));
        $randomNumber = rand(0, 10000);
        $email = 'yo.' . $randomNumber . '.yolo@gmail.com';

        return ['email' => $email, 'password' => $password, 'apiToken' => $apiToken];
    }
}
