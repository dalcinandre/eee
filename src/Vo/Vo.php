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

    /*
    public function __toString()
    {
        $obj = clone $this;
        $keys = get_object_vars($obj);
        foreach ($keys as $key => $value) {
            if (!$value) {
                unset($obj->{$key});
            }
        }

        return json_encode($obj);
    }
    */
}
