<?php

namespace Core\Vo;

class Vo
{
    public $id;
    public $limit;
    public $offset;
    public $createdAt;
    public $updateAt;

    public function __construct()
    {
    }

    /*public function __toString()
    {
        $obj = clone $this;
        foreach (get_object_vars($obj) as $key => $value) {
            if (!$value) {
                unset($obj->{$key});
            }
        }

        return $obj;
    }*/
}
