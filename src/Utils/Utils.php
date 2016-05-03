<?php

namespace Core\Utils;

class Utils
{
    public static function clean($obj)
    {
        return (object) array_filter((array) $obj, function ($key) {
          return !is_null($key);
        });
    }

    public static function mapper($json, $obj)
    {
        $m = new \JsonMapper();

        return $m->map((object) $json, $obj);
    }
}
