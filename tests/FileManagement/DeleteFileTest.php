<?php

namespace App\Tests\FileManagement;

use App\Tests\UserHelper;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DeleteFileTest extends WebTestCase
{
    /**
     * Test if an api user can delete image.
     *
     * @return void
     */
    public function testApiImageDelete()
    {
        $client = static::createClient();

        // create a random user
        extract(UserHelper::createRandomUser());

        UserHelper::registerUser($client, $email, $apiToken, $password);

        // get a random image
        $testImagePath = $client->getKernel()->getContainer()->getParameter('image_test');
        $image = new TestImage($testImagePath, true);

        // api accepts base64image upload
        $base64Image = base64_encode(file_get_contents($image->getPath()));

        $client->request('POST', '/api/image/upload', [
            'base64_image' => $base64Image,
            'name' => $image->getName(),
            'extension' => $image->getExtension(),
        ], [], [
            'HTTP_X-Requested-With' => 'XMLHttpRequest',
            'HTTP_X-AUTH-TOKEN' => $apiToken,
        ]);

        // check if file is in DB & is uploaded.
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertJson($client->getResponse()->getContent());

        $data = json_decode($client->getResponse()->getContent(), true);

        $client->request('DELETE', '/api/image/delete', ['email' => $email, 'img_id' => $data['id']], [], ['HTTP_X-AUTH-TOKEN' => $apiToken]);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertJson($client->getResponse()->getContent());

        $data = json_decode($client->getResponse()->getContent(), true);

        $this->assertFalse(file_exists($data[0]['path']));
    }

    /**
     * Test if a standard user can delete image.
     *
     * @return void
     */
    public function testUserImageDelete()
    {
        $client = static::createClient();

        // create a random user
        extract(UserHelper::createRandomUser());

        UserHelper::registerUser($client, $email, $apiToken, $password);

        UserHelper::loginUser($client, $email, $password);

        // get a random image
        $testImagePath = $client->getKernel()->getContainer()->getParameter('image_test');
        $image = new TestImage($testImagePath, true);

        // api accepts base64image upload
        $base64Image = base64_encode(file_get_contents($image->getPath()));

        $client->request('POST', '/api/image/upload', [
            'base64_image' => $base64Image,
            'name' => $image->getName(),
            'extension' => $image->getExtension(),
        ], [], [
            'HTTP_X-Requested-With' => 'XMLHttpRequest',
            'HTTP_X-AUTH-TOKEN' => $apiToken,
        ]);

        // check if file is in DB & is uploaded.
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertJson($client->getResponse()->getContent());

        $data = json_decode($client->getResponse()->getContent(), true);

        $client->request('DELETE', '/api/image/delete', ['email' => $email, 'img_id' => $data['id']], [], []);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertJson($client->getResponse()->getContent());

        $data = json_decode($client->getResponse()->getContent(), true);

        $this->assertFalse(file_exists($data[0]['path']));
    }
}
