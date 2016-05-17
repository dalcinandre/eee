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

    public static function getCities($lat, $long)
    {
        $file = json_decode(file_get_contents("http://maps.googleapis.com/maps/api/geocode/json?latlng={$lat},{$long}&sensor=true"));

        $saida = [];

        foreach ($file->results[0]->address_components as $address_component) {
            foreach ($address_component as $dados) {
                if (is_array($dados)) {
                    if (array_keys($dados) > 1) {
                        foreach ($dados as $key => $value) {
                            if ($value == 'administrative_area_level_2') {
                                $saida['city'] = $address_component->long_name;
                            } elseif ($value == 'administrative_area_level_1') {
                                $saida['state'] = $address_component->short_name;
                            }
                        }
                    }
                }
            }
        }

        return $saida;
    }
}
