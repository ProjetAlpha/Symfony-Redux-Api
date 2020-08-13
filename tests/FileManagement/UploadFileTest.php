<?php

namespace App\Tests\FileManagement;

use App\Tests\UserHelper;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UploadFileTest extends UserHelper
{
    /**
     * Test if a user api without a valid access token cant upload image.
     *
     * @group upload-image
     *
     * @return void
     */
    public function testUnauthorizedApiImageUpload()
    {
        extract(UserHelper::createRandomUser());
        $client = $this->client;

        // api accepts base64image upload
        $testImagePath = $client->getKernel()->getContainer()->getParameter('image_test');
        $image = new TestImage($testImagePath, true);

        $base64Image = base64_encode(file_get_contents($image->getPath()));

        $client->request('POST', '/api/image/upload', [
            'email' => $email,
            'base64_image' => $base64Image,
            'name' => $image->getName(),
            'extension' => $image->getExtension(),
        ], [], [
            'HTTP_X-API-TOKEN' => $apiToken,
        ]);

        // bad request response code
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
    }

    /**
     * Test if a standard user without a valid access token cant upload image.
     *
     * @group upload-image
     *
     * @return void
     */
    public function testUnauthorizedStandardUserImageUpload()
    {
        $client = $this->client;

        // create a random user
        extract(UserHelper::createRandomUser());

        UserHelper::registerUser($client, $email, $apiToken, $password, $firstname, $lastname);

        // api accepts base64image upload
        $testImagePath = $client->getKernel()->getContainer()->getParameter('image_test');
        $image = new TestImage($testImagePath, true);

        $base64Image = base64_encode(file_get_contents($image->getPath()));

        $client->request('POST', '/api/image/upload', [
            'base64_image' => $base64Image,
            'name' => $image->getName(),
            'extension' => $image->getExtension(),
            'email' => $email,
        ], [], ['HTTP_X-API-TOKEN' => $apiToken]);

        // bad request response code
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
    }

    /**
     * Test if a user api with a valid token can upload image.
     *
     * @group upload-image
     *
     * @return void
     */
    public function testBase64ApiUserImageUpload()
    {
        $client = $this->client;

        // get a random image
        $testImagePath = $client->getKernel()->getContainer()->getParameter('image_test');
        $image = new TestImage($testImagePath, true);

        // api accepts base64image upload
        $base64Image = base64_encode(file_get_contents($image->getPath()));

        $client->request('POST', '/api/image/upload', [
            'email' => $this->user->getEmail(),
            'base64_image' => $base64Image,
            'name' => $image->getName(),
            'extension' => $image->getExtension(),
        ], []);

        // check if file is in DB & is uploaded.
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    /**
     * Test if a standard user with a valid token can upload image.
     *
     * @group upload-image
     *
     * @return void
     */
    public function testBase64StandardUserImageUpload()
    {
        $client = $this->client;

        // get a random image
        $testImagePath = $client->getKernel()->getContainer()->getParameter('image_test');
        $image = new TestImage($testImagePath, true);

        // api accepts base64image upload
        $base64Image = base64_encode(file_get_contents($image->getPath()));

        $client->request('POST', '/api/image/upload', [
            'base64_image' => $base64Image,
            'name' => $image->getName(),
            'extension' => $image->getExtension(),
            'email' => $this->user->getEmail(),
        ], [], []);

        // check if file is in DB & is uploaded.
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        // check if user profil has image
    }
}
