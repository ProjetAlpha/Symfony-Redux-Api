<?php

namespace App\Tests;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * WARNING !
 * Dont use static::login without static::register, you need to create a new user if you
 * are using static::login, else your api token header is not set properly.
 */
class UserHelper extends WebTestCase
{
    /**
     * Html sample.
     *
     * @var string
     */
    protected $htmlSample;

    /**
     * Entity manager.
     *
     * @var
     */
    protected $em;

    /**
     * Admin entity.
     *
     * @var
     */
    protected $admin;
    
    /**
     * User enity.
     *
     * @var
     */
    protected $user;

    /**
     * Admin password.
     *
     * @var
     */
    protected $originalAdminPassword;

    /**
     * User password.
     *
     * @var
     */
    protected $originalUserPassword;

    protected static $isAdmin;

    protected $articleTitle;

    protected $articleDescription;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        $this->em = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
        
        $encoder = $kernel->getContainer()->get('security.password_encoder');
        $this->make($encoder);

        self::ensureKernelShutdown();
        $this->em->close();
        $this->em = null;
        $encoder = null;

        $apiToken = static::$isAdmin ? $this->admin->getApiToken() : $this->user->getApiToken();
        $this->client = static::createClient([], ['HTTP_X-API-TOKEN' => $apiToken]);

        $this->htmlSample = '<html><body><p>Hello World</p></body></html>';
        $this->articleDescription = 'Nice article john doe!';
        $this->articleTitle = 'John Doe Is The Best';
    }

    /**
     * Create an admin test user.
     */
    private function make($encoder): void
    {
        // create an admin user
        extract(UserHelper::createRandomUser());
        $this->originalAdminPassword = $password;

        $user = new User();

        $user->setPassword($encoder->encodePassword($user, $password));
        $user->setEmail($email);
        $user->setFirstname($firstname);
        $user->setLastname($lastname);

        $user->setApiToken(bin2hex(random_bytes(32)));
        $user->setExpireAtToken(time() + 60 * 60); // 1 hour token expiration
        $user->setConfirmationLink(null);
        $user->setRoles(['ROLE_USER', 'ROLE_API_USER', 'ROLE_ADMIN']);
        $user->setIsAdmin(true);

        $this->em->persist($user);
        //$this->em->flush();
        $this->admin = $user;

        // create a standard user
        extract(UserHelper::createRandomUser());
        $this->originalUserPassword = $password;

        $user = new User();
        $user->setPassword($encoder->encodePassword($user, $password));
        $user->setEmail($email);
        $user->setFirstname($firstname);
        $user->setLastname($lastname);

        $user->setApiToken(bin2hex(random_bytes(32)));
        $user->setExpireAtToken(time() + 60 * 60);  // 1 hour token expiration
        $user->setConfirmationLink(null);
        $user->setRoles(['ROLE_USER', 'ROLE_API_USER']);
        $user->setIsAdmin(false);

        $this->em->persist($user);
        $this->em->flush();
        $this->user = $user;
    }

    /**
     * Create a random admin article.
     *
     * @return int $articleId
     */
    protected function createArticle($isDraft = false)
    {
        $adminId = $this->admin->getId();

        $this->client->request('POST', '/api/admin/'.$adminId.'/articles/create', [
            'is_draft' => $isDraft,
            'raw_data' => $this->htmlSample,
            'title' => $this->articleTitle,
            'description' => $this->articleDescription
        ], [], []);

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        
        $jsonResponse = static::assertJsonResponse($this->client, 'id');

        return $jsonResponse['id'];
    }

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
     * @param \Symfony\Bundle\FrameworkBundle\Test\WebTestCase::createClient $client
     * @param string                                                         $email
     * @param string                                                         $password
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

    /**
     * Test if an api response error contains a json message.
     *
     * @param \Symfony\Bundle\FrameworkBundle\Test\WebTestCase::createClient $client
     *
     * @return void
     */
    public static function assertJsonResponseError($client, $key = null)
    {
        $response = $client->getResponse()->getContent();
        static::assertJson($response);

        $json = json_decode($response, true);
        static::assertArrayHasKey($key ? $key : 'error', $json);
    }

    /**
     * Test if an api response contains a json message.
     *
     * @param \Symfony\Bundle\FrameworkBundle\Test\WebTestCase::createClient $client
     *
     * @return void
     */
    public static function assertJsonResponse($client, $key = null, $value = null)
    {
        $response = $client->getResponse()->getContent();
        static::assertJson($response);

        $json = json_decode($response, true);

        if (isset($key) && is_array($key) && is_array($json)) {
            $fail = false;
            foreach ($json as $k => $v) {
                if (!in_array($v, $key) || !array_key_exists($k, $key)) {
                    $fail = true;
                }
            }
            static::assertFalse($fail);
        } else {
            if ($key) {
                static::assertArrayHasKey($key, $json);
            }

            if ($value) {
                static::assertEquals($json[$key], $value);
            }
        }

        return $json;
    }
}
