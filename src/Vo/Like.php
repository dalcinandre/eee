<?php

namespace Core\Vo;

class Like extends Vo
{
    /**
     * @var User
     */
    public $user;

    /**
     * @var User
     */
    public $like;

    /**
     * @var bool
     */
    public $liked;

    public function __construct()
    {
    }
}
