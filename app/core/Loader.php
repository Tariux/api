<?php

namespace JAPI;

class Loader
{
    function __construct()
    {
        spl_autoload_register('JAPI\Loader::auto_load_requests');

        require_once './app/inc/functions.php';
        import_base('app/core/Route');
        import_base('app/core/Response');

    }

    public static function init_www()
    {
        header('Content-Type: application/json; charset=utf-8');
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: PUT, GET, POST");


        date_default_timezone_set("Asia/Tehran");


        $POST_DATA = json_decode(file_get_contents('php://input'), true);
        $_POST = $POST_DATA;


        if (!defined("API_REQUEST")) {

            $API_REQUEST = $_SERVER['REQUEST_URI'];
            define('API_REQUEST', $API_REQUEST);
        }

        define('SECRET_KEY', '3FD63C6AB84E4F9F85B7439A5A9DD');
        define('SMS_USERNAME', '09123356214');
        define('SMS_PASSWORD', 'Redemption$110');
        define('SMS_NUMBER', '30008666907545');
        define('SMS_API', 'https://console.melipayamak.com/api/send/simple/5034b821c81a4329a6ac45d38128fe83');
        define('ZARINPAL_MERCHANT_ID', '087fee0e-1882-488d-9a7c-2a6701620703');
        define('SMTP_MAIL', 'contact@neelabook.ir');
        define('SMTP_PASSWORD', 'G#@Q*;rp;0X=');
        define('WEBSITE_URL', 'http://localhost/meow');

    }



    public static function auto_load_requests($className) {
        $filename = "requests/" . $className . ".php";
        if (is_readable($filename)) {
            require $filename;
        }
    }
    
}
