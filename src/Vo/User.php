<?php

namespace Core\Vo;

class User extends Vo
{
    public $name;
    public $username;
    public $password;
    public $birthday;
    public $interestFrom;
    public $interestTo;
    public $aboutMe;
    public $congregation;

    /**
     * @var Genre
     */
    public $genre;
    public $profession;
    public $location;
    public $radius;
    public $photos;

    public function __construct()
    {
    }
}
