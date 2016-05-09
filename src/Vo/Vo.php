<?php

namespace Core\Vo;

class Vo
{
    /**
     * @var int|null
     */
    public $id;

    /**
     * @var int|null
     */
    public $limit;

    /**
     * @var int|null
     */
    public $offset;

    /**
     * @var string|null
     */
    public $createdAt;

    /**
     * @var string|null
     */
    public $updateAt;

    /**
     * @var int|null
     */
    public $status;

    public function __construct()
    {
    }
}
