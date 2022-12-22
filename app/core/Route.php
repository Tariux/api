<?php 


namespace JAPI;
import_base('app/core/Response');
import_base('app/core/Loader');

class Route {

    public $CLIENT_IP;
    public $API_ROUTE;


    function __construct()
    {

        Loader::init_www();

        if (!defined("CLIENT_IP")) {
            
            $CLIENT_IP = $_SERVER['REMOTE_ADDR'];
            $this->CLIENT_IP = $CLIENT_IP;
            define('CLIENT_IP', $CLIENT_IP);
    
        }
        if (!defined("API_ROUTE")) {
            
            $API_ROUTE = $this->validate();
            $this->API_ROUTE = $API_ROUTE;
            define('API_ROUTE', $API_ROUTE);
    
        }

    }

    public function url_to_array($URL = "")
    {
        $URL = (strpos($URL, "?")) ? substr($URL, 0, strpos($URL, "?")) : $URL;

        $request_map = preg_split("#/#", $URL);
        if (empty($request_map) || $request_map === null || !isset($request_map)) {
            return false;
        } else {
            foreach ($request_map as $key => $value) {
                if (empty($value)) {
                    unset($request_map[$key]);
                }
            }
            $request_map = array_values($request_map);
            return $request_map;
        }


    }

    public function validate()
    {

        $API_ROUTE = $this->url_to_array(API_REQUEST);
        $VALIDATE = [];

        if ($this->CLIENT_IP == "127.0.0.1") {
            unset($API_ROUTE[0]);
            $API_ROUTE = array_values($API_ROUTE);
        }

        foreach ($API_ROUTE as $key => $value) {
            switch ($key) {
                case 0:
                    $VALIDATE['CLASS'] = $value;
                break;
                case 1:
                    $VALIDATE['FUNC'] = $value;
                break;
            
                default:
                    $VALIDATE['value_' . rand(100,999)] = $value;
                break;
            }
        }
        return $VALIDATE;
    }

    public function do()
    {
        $APP_REQUEST = $this->validate();

        if (!class_exists($APP_REQUEST['CLASS'])) {
            new Response([],'(class) Not found.' , false);
        }

        $request_class = new $APP_REQUEST['CLASS'];
        $APP_REQUEST['FUNC'] = (empty($APP_REQUEST['FUNC']) || !isset($APP_REQUEST['FUNC'])) ? 'index' : $APP_REQUEST['FUNC'];

        if (!method_exists($request_class, $APP_REQUEST['FUNC'])) {
            new Response([],'(func) Not found.' , false);
        }

        if (method_exists($request_class, $APP_REQUEST['FUNC']) && is_callable(array($request_class, $APP_REQUEST['FUNC']))) {
            
            call_user_func([$request_class, $APP_REQUEST['FUNC']]);

        } else {

            new Response([],'Access denied.');

        }

    }
}