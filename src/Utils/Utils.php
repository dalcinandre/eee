<?php

namespace Core\Utils;

class Utils
{
    private static $utils;
    private $jsonMapper;

    private function __construct()
    {
        $this->jsonMapper = new \JsonMapper();
    }

    /*
    public static function clean($obj)
    {
        return (object) array_filter((array) $obj, function ($key) {
          return !is_null($key);
        });
    }
    */

    /*
    public static function walkr($obj)
    {
        $obj = (array) $obj;
        array_walk_recursive($obj, function ($v, $k) use (&$obj) {
          if ($obj[$k] == null) {
              unset($obj[$k]);
          }
        });

        return (object) $obj;
    }
    */

    public static function clean($obj)
    {
        $objVars = get_object_vars($obj);

        if (count($objVars) > 0) {
            foreach ($objVars as $propName => $propVal) {
                if (gettype($propVal) == 'object') {
                    $cObj = self::clean($propVal);
                    if (empty($cObj)) {
                        unset($obj->$propName);
                    } else {
                        $obj->$propName = $cObj;
                    }
                } elseif (is_array($propVal)) {
                    foreach ($propVal as $key => $value) {
                        self::clean($value);
                    }
                } else {
                    if (empty($propVal)) {
                        unset($obj->$propName);
                    }
                }
            }
        } else {
            return;
        }

        return $obj;
    }

    public static function mapper($json, $class)
    {
        if (!self::$utils instanceof self) {
            self::$utils = new self();
        }

        return self::$utils->jsonMapper->map($json, $class);
    }
}
