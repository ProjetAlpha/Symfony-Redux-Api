<?php

namespace App\Security;

class TokenProvider
{
    /**
     *
     * @var string
     */
    private $token;

    /**
     *
     * @var int
     */
    private $expireAt;

    public function __construct()
    {
    }

    /**
     *
     *
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     *
     *
     * @param $length
     * @return void
     */
    public function setToken($length)
    {
        $this->token = rtrim(strtr(base64_encode(random_bytes($length)), '+/', '-_'), '=');
    }

    /**
     *
     *
     * @return void
     */
    public function getExpireAt()
    {
        return $this->expireAt;
    }

    /**
     *
     * @param $time
     * @return void
     */
    public function setExpireAt($time)
    {
        $this->expireAt = $time;
    }
}
