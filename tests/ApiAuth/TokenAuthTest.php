<?php

namespace App\Tests\FileManagement;

use Symfony\Bridge\PhpUnit\ClockMock;
use App\Security\TokenAuthenticator;
use App\Security\AdminAuthenticator;
use App\Tests\UserHelper;

class TokenAuthTest extends UserHelper
{
    /**
     * @group Token
     *
     * @return void
     */
    public function testIfAnApiUserIsAuthorized()
    {
        $this->client->request('POST', '/api/me/', [], [], ['HTTP_X-API-TOKEN' => $this->user->getApiToken()]);

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @group Token
     *
     * @return void
     */
    public function testIfAnApiAdminIsAuthorized()
    {
        $adminId = $this->admin->getId();

        $this->client->request('POST', '/api/admin/'.$adminId.'/articles/create', [
            'is_draft' => true,
            'raw_data' => $this->htmlSample,
        ], [], [
            'HTTP_X-API-TOKEN' => $this->admin->getApiToken()
        ]);

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @group Token-Expiration
     * @runInSeparateProcess
     *
     * @return void
     */
    public function testIfAnExpiredUserTokenIsUnauhtorized()
    {
        ClockMock::register(TokenAuthenticator::class);
        ClockMock::withClockMock(time() + 60 * 60 + 110);

        $this->client->request('POST', '/api/me/', [], [], ['HTTP_X-API-TOKEN' => $this->user->getApiToken()]);

        $this->assertEquals(401, $this->client->getResponse()->getStatusCode());

        $this->assertJsonResponse($this->client, 'refresh_token');
    }

    /**
     * @group Token-Expiration
     * @runInSeparateProcess
     *
     * @return void
     */
    public function testIfAnExpiredAdminTokenIsUnauhtorized()
    {
        ClockMock::register(AdminAuthenticator::class);
        ClockMock::withClockMock(time() + 60 * 60 + 110);

        $this->client->request('POST', '/api/me/', [], [], ['HTTP_X-API-TOKEN' => $this->admin->getApiToken()]);

        $this->assertEquals(401, $this->client->getResponse()->getStatusCode());

        $this->assertJsonResponse($this->client, 'refresh_token');
    }
}
