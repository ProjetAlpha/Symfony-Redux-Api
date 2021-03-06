<?php

namespace App\Security;

class UserSession
{
    /**
     * Tell if a user is currently logged in.
     *
     * @var bool
     */
    private $isLoggedIn;

    /**
     * User id.
     *
     * @var int
     */
    private $id;

    /**
     * User email.
     *
     * @var string
     */
    private $email;

    /**
     * Token hash.
     *
     * @var string
     */
    private $token;

    /**
     * Token timestamp expiration.
     *
     * @var int
     */
    private $expireAt;

    /**
     * Tell if a user is an administrator.
     *
     * @var [type]
     */
    private $isAdmin;

    public function __construct()
    {
    }

    public function getIsLoggedIn(): bool
    {
        return $this->isLoggedIn;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function getExpireAt(): int
    {
        return $this->expireAt;
    }

    public function getIsAdmin()
    {
        return $this->isAdmin;
    }

    public function setIsLoggedIn($bool)
    {
        $this->isLoggedIn = $bool;

        return $this;
    }

    public function setIsAdmin($bool)
    {
        $this->isAdmin = $bool;

        return $this;
    }

    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }

    public function setExpireAt($time)
    {
        $this->expireAt = $time;

        return $this;
    }
}
