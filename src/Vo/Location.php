<?php

namespace Core\Vo;

class Location extends Vo
{
    /**
     * @var float
     */
    public $latitude;
    /**
     * @var float
     */
    public $longitude;

    /**
     * @var string|null
     */
    public $city;

    /**
     * @var string|null
     */
    public $state;

    public function __construct()
    {
    }
}
