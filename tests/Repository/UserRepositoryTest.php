<?php

namespace App\Tests\Repository;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Repository\UserRepository;
use App\Tests\FileManagement\TestImage;
use App\Tests\UserHelper;

class UserRepositoryTest extends WebTestCase
{
    public function testUnauthoriziedApiUser()
    {
        $client = static::createClient();

        // create a random user
        extract(UserHelper::createRandomUser());

        // find api user token
        $client->request('POST', '/api/me/', [], [], ['HTTP_X-AUTH-TOKEN' => $apiToken]);

        // check unauthorizied response status
        $this->assertEquals(401, $client->getResponse()->getStatusCode());
    }

    public function testIfUserIsNOTAuthenticated()
    {
        $client = static::createClient();

        // create a random user
        extract(UserHelper::createRandomUser());

        UserHelper::registerUser($client, $email, $apiToken, $password);

        // find api user token
        $client->request('POST', '/api/me/', [], [], []);

        // check unauthorizied response status
        $this->assertEquals(401, $client->getResponse()->getStatusCode());
    }

    public function testIfUserWithoutAuthentificationStrategieIsUnauthorized()
    {
        $client = static::createClient();

        // create a random user
        extract(UserHelper::createRandomUser());

        // find api user token
        $client->request('POST', '/api/me/', [], [], []);

        // check unauthorizied response status
        $this->assertEquals(401, $client->getResponse()->getStatusCode());
    }

    public function testAuthoriziedApiUser()
    {
        $client = static::createClient();

        // create a random user
        extract(UserHelper::createRandomUser());

        UserHelper::registerUser($client, $email, $apiToken, $password);

        UserHelper::loginUser($client, $email, $password);

        // find api user token
        $client->request('POST', '/api/me/', [], [], ['HTTP_X-AUTH-TOKEN' => $apiToken]);

        // check response status
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        
        // check json response
        $this->assertEquals('application/json', $client->getResponse()->headers->get('content-type'));

        $this->assertJson($client->getResponse()->getContent());

        $data = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('api_token', $data);
        $this->assertEquals($email, $data['email']);
    }

    public function testBasicUserRegister()
    {
        $client = static::createClient();

        extract(UserHelper::createRandomUser());

        UserHelper::registerUser($client, $email, $apiToken, $password);
        
        $this->assertEquals(201, $client->getResponse()->getStatusCode());
    }

    public function testIfUserHasUniqueEmail()
    {
        $client = static::createClient();

        extract(UserHelper::createRandomUser());

        UserHelper::registerUser($client, $email, $apiToken, $password);
        
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
        ], json_encode(UserHelper::createRandomUser()));

        $this->assertEquals(201, $client->getResponse()->getStatusCode());
    }

    public function testJsonParametersRequestRegister()
    {
        $client = static::createClient();

        $client->request('POST', '/register', UserHelper::createRandomUser(), [], [
            'HTTP_X-Requested-With' => 'XMLHttpRequest'
        ]);

        $this->assertEquals(201, $client->getResponse()->getStatusCode());
    }

    public function testIfOneUserHasUploadedImages()
    {
        $client = static::createClient();

        extract(UserHelper::createRandomUser());

        UserHelper::registerUser($client, $email, $apiToken, $password);

        UserHelper::loginUser($client, $email, $password);

        $testImagePath = $client->getKernel()->getContainer()->getParameter('image_test');
        
        $firstImage = new TestImage($testImagePath, true);
        $base64Image = base64_encode(file_get_contents($firstImage->getPath()));

        $client->request('POST', '/api/image/upload', [
            'base64_image' => $base64Image,
            'name' => $firstImage->getName(),
            'extension' => $firstImage->getExtension()
        ], [], [
            'HTTP_X-Requested-With' => 'XMLHttpRequest',
            'HTTP_X-AUTH-TOKEN' => $apiToken
        ]);

        $secondImage = new TestImage($testImagePath, true);
        $base64Image = base64_encode(file_get_contents($secondImage->getPath()));

        $client->request('POST', '/api/image/upload', [
            'base64_image' => $base64Image,
            'name' => $secondImage->getName(),
            'extension' => $secondImage->getExtension()
        ], [], [
            'HTTP_X-Requested-With' => 'XMLHttpRequest',
            'HTTP_X-AUTH-TOKEN' => $apiToken
        ]);

        $client->request('POST', '/api/image/search', [], [], [
            'HTTP_X-AUTH-TOKEN' => $apiToken,
            'HTTP_X-Requested-With' => 'XMLHttpRequest'
        ]);

        $this->assertJson($client->getResponse()->getContent());

        $data = json_decode($client->getResponse()->getContent(), true);

        $this->assertEquals(2, count($data['images']));
    }
}
