<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserHelper extends WebTestCase
{
    /**
     * Create a true random user.
     */
    public static function createRandomUser(): array
    {
        $apiToken = bin2hex(random_bytes(32));
        $password = bin2hex(random_bytes(32));
        $randomNumber = rand(0, 100000);
        $name = bin2hex(random_bytes(10));
        $email = $name.'-'.$randomNumber.'.yolo@gmail.com';

        return ['email' => $email, 'password' => $password, 'apiToken' => $apiToken];
    }

    /**
     * Register a generated user and test if a user is registered successfully.
     *
     * @param \Symfony\Bundle\FrameworkBundle\Test\WebTestCase::createClient $client
     * @param string                                                         $email
     * @param string                                                         $apiToken
     * @param string                                                         $password
     *
     * @return void
     */
    public static function registerUser($client, $email, $apiToken, $password)
    {
        $client->request(
            'POST',
            '/register',
            [
                'api_token' => $apiToken,
                'email' => $email,
                'password' => $password,
            ]
        );

        static::assertEquals(201, $client->getResponse()->getStatusCode());
    }

    /**
     * Log a generated user and test if a user is logged in successfully.
     *
     * @param [string] $client
     * @param [string] $email
     * @param [string] $password
     *
     * @return void
     */
    public static function loginUser($client, $email, $password)
    {
        $client->request(
            'POST',
            '/login',
            [
                'email' => $email,
                'password' => $password,
            ]
        );

        static::assertEquals(200, $client->getResponse()->getStatusCode());
    }
}
