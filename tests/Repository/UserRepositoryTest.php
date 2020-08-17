<?php

namespace App\Tests\Repository;

use App\Repository\UserRepository;
use App\Tests\FileManagement\TestImage;
use App\Tests\UserHelper;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserRepositoryTest extends UserHelper
{
    /**
     * Test if an api user has a valid api token.
     *
     * @group user-repo
     *
     * @return void
     */
    public function testUnauthoriziedApiUser()
    {
        $client = $this->client;

        // create a random user
        extract(UserHelper::createRandomUser());

        // find api user token
        $client->request('POST', '/api/me/', [], [], ['HTTP_X-API-TOKEN' => $apiToken]);

        // unauthorized response status code
        $this->assertEquals(401, $client->getResponse()->getStatusCode());
    }

    /**
     * Test if an api user has a valid api token.
     *
     * @group user-repo
     *
     * @return void
     */
    public function testAuthoriziedApiUser()
    {
        $client = $this->client;

        // find api user token
        $client->request('POST', '/api/me/', [], [], ['HTTP_X-API-TOKEN' => $this->user->getApiToken()]);

        // check response status
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        // check json response
        $this->assertEquals('application/json', $client->getResponse()->headers->get('content-type'));

        $this->assertJson($client->getResponse()->getContent());

        $data = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('api_token', $data);
        $this->assertEquals($this->user->getEmail(), $data['email']);
    }

    /**
     * Test if a user email already exist.
     *
     * @group user-repo
     *
     * @return void
     */
    public function testIfUserHasUniqueEmail()
    {
        $client = $this->client;

        extract(static::createRandomUser());

        static::registerUser($client, $email, $apiToken, $password, $firstname, $lastname);

        // second request with same credentials
        static::registerUser($client, $email, $apiToken, $password, $firstname, $lastname, 422);

        // json validation error
        static::assertJsonResponseError($client, 'email');
    }

    /**
     * Test if an api request support JSON body.
     *
     * @group user-repo
     *
     * @return void
     */
    public function testJsonBodyRequestRegister()
    {
        $client = $this->client;

        $client->request('POST', '/api/register', [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_X-Requested-With' => 'XMLHttpRequest',
        ], json_encode(UserHelper::createRandomUser()));

        $this->assertEquals(201, $client->getResponse()->getStatusCode());
    }

    /**
     * Test if an api request support JSON parameters.
     *
     * @group user-repo
     *
     * @return void
     */
    public function testJsonParametersRequestRegister()
    {
        $client = $this->client;

        $client->request('POST', '/api/register', UserHelper::createRandomUser(), [], [
            'HTTP_X-Requested-With' => 'XMLHttpRequest',
        ]);

        $this->assertEquals(201, $client->getResponse()->getStatusCode());
    }

    /**
     * Test if a user has and can upload multiple images.
     *
     * @group user-repo
     *
     * @return void
     */
    public function testIfOneUserHasUploadedImages()
    {
        $client = $this->client;

        $testImagePath = $client->getKernel()->getContainer()->getParameter('image_test');

        $firstImage = new TestImage($testImagePath, true);
        $base64Image = base64_encode(file_get_contents($firstImage->getPath()));

        $client->request('POST', '/api/image/upload', [
            'email' => $this->user->getEmail(),
            'base64_image' => $base64Image,
            'name' => $firstImage->getName(),
            'extension' => $firstImage->getExtension(),
        ], []);

        $secondImage = new TestImage($testImagePath, true);
        $base64Image = base64_encode(file_get_contents($secondImage->getPath()));

        $client->request('POST', '/api/image/upload', [
            'email' => $this->user->getEmail(),
            'base64_image' => $base64Image,
            'name' => $secondImage->getName(),
            'extension' => $secondImage->getExtension(),
        ], []);

        $client->request('POST', '/api/image/search', ['email' => $this->user->getEmail()], []);

        $this->assertJson($client->getResponse()->getContent());

        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals(2, count($data['images']));
    }

    /**
     * Test if a user has a profil.
     *
     * @group user-repo
     *
     * @return void
     */
    public function testIfAUserHasProfilInfo()
    {
        $client = $this->client;

        $userRepository = static::$container->get(UserRepository::class);

        $user = $userRepository->findOneBy(['email' => $this->user->getEmail()]);

        $client->request('POST', '/api/profil', ['id' => $user->getId()]);
        $responseData = $client->getResponse()->getContent();

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $this->assertJson($responseData);

        $data = json_decode($responseData, true);

        $this->assertArrayHasKey('lastname', $data);
        $this->assertArrayHasKey('firstname', $data);
        $this->assertArrayHasKey('image', $data);

        $this->assertEquals(0, count($data['image']));
    }
}
