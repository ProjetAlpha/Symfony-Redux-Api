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
        $id = bin2hex(random_bytes(10));
        $email = $id.'-'.$randomNumber.'.yolo@gmail.com';

        $firstname = bin2hex(random_bytes(9));
        $lastname = bin2hex(random_bytes(9));

        return [
                'email' => $email,
                'password' => $password,
                'apiToken' => $apiToken,
                'firstname' => $firstname,
                'lastname' => $lastname,
            ];
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
    public static function registerUser($client, $email, $apiToken, $password, $firstname, $lastname, $expectedResponse = null)
    {
        $client->request(
            'POST',
            '/api/register',
            [
                'api_token' => $apiToken,
                'email' => $email,
                'password' => $password,
                'firstname' => $firstname,
                'lastname' => $lastname,
            ]
        );

        static::assertEquals($expectedResponse ? $expectedResponse : 201, $client->getResponse()->getStatusCode());
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
    public static function loginUser($client, $email, $password, $expectedResponse = null)
    {
        $client->request(
            'POST',
            '/api/login',
            [
                'email' => $email,
                'password' => $password,
            ]
        );

        static::assertEquals($expectedResponse ? $expectedResponse : 200, $client->getResponse()->getStatusCode());
    }
}
