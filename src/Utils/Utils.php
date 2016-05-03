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

    public static function map($json, $obj)
    {
        $obj = (object) $obj;

        foreach (get_object_vars((object) $json) as $key => &$value) {
            // if (is_array($value)) {
            //     self::map($value, $obj->{$key});
            // }

            $obj->{$key} = $value;
        }

        return $obj;
    }

    public static function mapper($json, $obj)
    {
        $m = new \JsonMapper();

        return $m->map((object) $json, $obj);
    }
}
