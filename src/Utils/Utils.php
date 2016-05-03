<?php

namespace Utils;

class Utils
{
    public static function clean($obj)
    {
        return (object) array_filter((array) $obj, function ($key) {
          return !is_null($key);
        });
    }
}
