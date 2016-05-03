<?php

namespace Core\Dao;

$config = explode(DIRECTORY_SEPARATOR, __FILE__);
$config = parse_ini_file(DIRECTORY_SEPARATOR.$config[1].DIRECTORY_SEPARATOR.$config[2].DIRECTORY_SEPARATOR.'config_eee.ini');

define('HOST', $config['host']);
define('PORT', $config['port']);
define('DB', $config['database']);
define('USER_NAME', $config['username']);
define('PASSWORD', $config['password']);
define('DSN', sprintf($config['url'], HOST, PORT, DB));

class Conexao
{
    private function __construct()
    {
    }

    public static function getConexao()
    {
        try {
            $con = new \PDO(DSN, USER_NAME, PASSWORD);
            $con->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            $con->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);

            return $con;
        } catch (\PDOException $e) {
            throw $e;
        }
    }
}
