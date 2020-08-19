<?php

namespace App\Tests\FileManagement;

use App\Repository\ArticleRepository;
use App\Tests\UserHelper;
use App\Repository\ImageRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class FetchFileTest extends UserHelper
{
    /**
     * Test if a user can fetch his private image.
     *
     * @group fetch-file
     *
     * @return void
     */
    public function testIfAUserCanFetchHisPrivateImage()
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
            'GET',
            '/api/image/private/'.$data['id'],
            [],
            []
        );

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertJson($this->client->getResponse()->getContent());

        $data = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertTrue(file_exists($data['path']));
        
        $src = 'data: '.mime_content_type($data['path']).';base64,'.$base64Image;
        $this->assertEquals($data['image'], $src);
    }
}
