<?php

namespace App\Tests\FileManagement;

use App\Tests\UserHelper;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DeleteFileTest extends UserHelper
{
    /**
     * Test if an api user can delete image.
     *
     * @group delete-image
     *
     * @return void
     */
    public function testApiImageDelete()
    {
        // get a random image
        $testImagePath = $this->client->getKernel()->getContainer()->getParameter('image_test');
        $image = new TestImage($testImagePath, true);

        // api accepts base64image upload
        $base64Image = base64_encode(file_get_contents($image->getPath()));

        $this->client->request('POST', '/api/image/upload', [
            'email' => $this->user->getEmail(),
            'base64_image' => $base64Image,
            'name' => $image->getName(),
            'extension' => $image->getExtension(),
        ]);

        // check if file is in DB & is uploaded.
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertJson($this->client->getResponse()->getContent());

        $data = json_decode($this->client->getResponse()->getContent(), true);

        $this->client->request(
            'DELETE',
            '/api/image/delete',
            ['email' => $this->user->getEmail(),
            'img_id' => $data['id']],
            []
        );

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertJson($this->client->getResponse()->getContent());

        $data = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertFalse(file_exists($data[0]['path']));
    }

    /**
     * Test if a standard user can delete image.
     *
     * @group delete-image
     *
     * @return void
     */
    public function testUserImageDelete()
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
        $this->assertJson($client->getResponse()->getContent());

        $data = json_decode($client->getResponse()->getContent(), true);

        $client->request(
            'DELETE',
            '/api/image/delete',
            [
            'email' => $this->user->getEmail(), 'img_id' => $data['id']
            ],
            [],
            []
        );

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertJson($client->getResponse()->getContent());

        $data = json_decode($client->getResponse()->getContent(), true);

        $this->assertFalse(file_exists($data[0]['path']));
    }
}
