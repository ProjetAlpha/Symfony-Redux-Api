<?php

namespace App\Security;

class UserSession 
{
    private $isLoggedIn;

    private $id;

    private $email;

    private $token;

    private $expireAt;

    public function __construct()
    {
        
    }

    public function getIsLoggedIn() : bool
    {
        return $this->isLoggedIn;
    }

    public function getId() : int 
    {
        return $this->id;
    }

    public function getEmail() : string
    {
        return $this->email;
    }

    public function getToken() : string
    {
        return $this->token;
    }

    public function getExpireAt() : int
    {
        return $this->expireAt;
    }

    public function setIsLoggedIn($bool)
    {
        $this->isLoggedIn = $bool;

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