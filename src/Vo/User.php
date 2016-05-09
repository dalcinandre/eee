<?php

namespace Core\Vo;

class User extends Vo
{
    /**
     * @var string|null
     */
    public $name;

    /**
     * @var string|null
     */
    public $lastName;

    public $username;

    /**
     * @var string|null
     */
    public $password;

    /**
     * @var string|null
     */
    public $birthday;

    /**
     * @var int|null
     */
    public $interestFrom;

    /**
     * @var int|null
     */
    public $interestTo;

    /**
     * @var string|null
     */
    public $bio;

    /**
     * @var string|null
     */
    public $congregation;

    /**
     * @var Gender|null
     */
    public $gender;

    /**
     * @var string|null
     */
    public $profession;

    /**
     * @var Location
     */
    public $location;

    /**
     * @var int|null
     */
    public $radius;

    /**
     * @var Photo[]|null
     */
    public $photos;

    public function __construct()
    {
    }
}
