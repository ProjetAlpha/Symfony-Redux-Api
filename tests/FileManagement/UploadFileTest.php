<?php

namespace App\Tests\FileManagement;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Tests\FileManagement\TestImage;
use App\Repository\UserRepository;
use App\Repository\ImageRepository;

/*
    -- Api request support test :
    const MimeTypesMap = {
            png: 'image/png',
            gif: 'image/gif',
            jpg: 'image/jpg',
            jpeg: 'image/jpeg',
            pdf: 'application/pdf',
            mp4: 'video/mp4',
            doc: 'application/msword',
            docx: 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            ppt: 'application/vnd.ms-powerpoint',
            xlsx: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
    };

    -- Api request support blob file (binary file).
*/

class UploadFileTest extends WebTestCase
{
    public function testUnauthorizedApiImageUpload()
    {
        $client = static::createClient();

        // api accepts base64image upload
        $testImagePath = $client->getKernel()->getContainer()->getParameter('image_test');
        $image = new TestImage($testImagePath, true);

        $base64Image = base64_encode(file_get_contents($image->getPath()));

        $client->request('POST', '/api/image/upload', [
            'base64_image' => $base64Image,
            'name' => $image->getName(),
            'extension' => $image->getExtension()
        ], [], [
            'HTTP_X-Requested-With' => 'XMLHttpRequest'
        ]);

        // unauthorized response code
        $this->assertEquals(401, $client->getResponse()->getStatusCode());
    }

    public function testBase64ImageUpload()
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

        // find api user
        $client->request('POST', '/api/me/', [], [], ['HTTP_X-AUTH-TOKEN' => $apiToken]);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        // get a random image
        $testImagePath = $client->getKernel()->getContainer()->getParameter('image_test');
        $image = new TestImage($testImagePath, true);

        // api accepts base64image upload
        $base64Image = base64_encode(file_get_contents($image->getPath()));

        $client->request('POST', '/api/image/upload', [
            'base64_image' => $base64Image,
            'name' => $image->getName(),
            'extension' => $image->getExtension()
        ], [], [
            'HTTP_X-Requested-With' => 'XMLHttpRequest',
            'HTTP_X-AUTH-TOKEN' => $apiToken
        ]);
        
        // check if file is in DB & is uploaded.
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        /*$param = $client->getKernel()->getContainer()->getParameter('upload_image_dir');
        $user = static::$container->get(UserRepository::class)->findOneBy(['apiToken' => $apiToken]);
        $destination = $param . $user->getId() . '/' . $image->getName() . '-' . uniqid() . '.' . $image->getExtension();
        $image = static::$container->get(ImageRepository::class)->findOneBy(['user_id' => $user->getId()]);
        
        $this->assertEquals($destination, $image->getPath());
        
        if (!file_exists($destination))
            $this->fail('Image not saved.');*/
    }

    private function createRandomUser() : array
    {
        $apiToken = bin2hex(random_bytes(32));
        $password = bin2hex(random_bytes(32));
        $randomNumber = rand(0, 10000);
        $name = bin2hex(random_bytes(10));
        $email = $name . '-' . $randomNumber . '.yolo@gmail.com';

        return ['email' => $email, 'password' => $password, 'apiToken' => $apiToken];
    }
}