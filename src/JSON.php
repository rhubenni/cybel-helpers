<?php declare(strict_types=1);

namespace Rtelesco\Cybel;

use \Rtelesco\Cybel\HTTP\Headers;
use \Rtelesco\Cybel\HTTP\ContentType;

class JSON
{
    /**
     * defines JSON filters when encoding data
     * @var
     */
    public static $flag = JSON_NUMERIC_CHECK;
    
    /**
     * print array as JSON
     * @param array $var the array
     * @param int $status defines http status
     * @param type $exit exit php after print? (default false)
     * @return bool
     */
    public static function json_print(array $var, int $status = 200, $exit = false) : bool
    {
        if(!is_array($var)) { return false; }
        
        Headers::status($status);
        Headers::noCache();
        ContentType::define('json');
        
        $json = json_encode($var, self::$flag);
        if(!$json && $exit === false) { return false; }
        elseif (!$json && $exit === true)
        {
            trigger_error('Erro processando saida de dados', E_USER_ERROR);
        } elseif(!$json === false && $exit === true) {
            echo $json;
            die();
        } else {
            echo $json;
            return true;
        }
    }
    
    /**
     * parse POST JSON payload
     * @return array
     */
    public static function parse_post() : array
    {
        if(\filter_input(INPUT_SERVER, 'REQUEST_METHOD') === 'POST') {
            $data = json_decode(file_get_contents('php://input'), true);
        } else {
            $data = [null];
        }
        return [
            'size'    => (is_array($data) > 0) ? count($data) : 0,
            'data'    => $data
        ];
    }
    
    /**
     * parse PUT JSON payload
     * @return array
     */
    public static function parse_put() : array
    {
        if(\filter_input(INPUT_SERVER, 'REQUEST_METHOD') === 'PUT') {
            $data = json_decode(file_get_contents('php://input'), true);
        } else {
            $data = [null];
        }
        return [
            'size'    => (is_array($data) > 0) ? count($data) : 0,
            'data'    => $data
        ];
    }
    
    /**
     * returns a key of JSON POST payload
     * @param type $key
     * @return string
     */
    public static function parse_post_item($key) : string
    {
        $data = self::parse_post();
        return (string) $data['data'][$key] ?? '';
    }
    
    /**
     * reads a JSON file and returns then as stdClass
     * @param type $filename
     * @return \stdClass
     */
    public static function parse_file($filename) : \stdClass
    {
        $json = file_get_contents($filename);
        $obj = \json_decode($json);
        return $obj;
    }
    
    /**
     * encodes array as JSON
     * @param array $var
     * @return string
     */
    public static function generate_var(array $var) : string
    {
        return \json_encode($var, self::$flag);
    }
    
    /*
     * @deprecated use put_file() instead
     */
    public static function put_temp_file(string $name, array $data) : bool
    {
        return self::put_file($path = '', $name, $data);
    }
    
    /**
     * write JSON file
     * @param string $path path to folder
     * @param string $name JSON filename
     * @param array $data data to be saved
     * @return bool
     */
    public static function put_file(string $path, string $name, array $data) : bool
    {
        $filename = $path . DIRECTORY_SEPARATOR . $name;
        $w = \file_put_contents($filename, self::generate_var($data));
        usleep(10);
        return (!$w) ? false : true;
    }
    
}