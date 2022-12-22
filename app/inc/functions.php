<?php

function import_base($path)
{
    $api_path = dirname(dirname(__DIR__)) ;
    require_once($api_path . '\\' . $path . '.php');
}
function isUsername($username)
{
    $username = stripslashes($username);
    $username = htmlspecialchars($username);
    if (strlen($username) > 4 && strlen($username) < 66) {
        return $username;
    } else {
        return false;
    }
}
function isEmail($email)
{
    $email_pattern = "/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/i";
    $email = filter_var($email, FILTER_VALIDATE_EMAIL);
    if (!empty($email) && strlen($email) <= 255 && preg_match($email_pattern, $email)) {
        return $email;
    } else {
        return false;
    }
}
function isText($txt)
{
    $txt = stripslashes($txt);
    $txt = htmlspecialchars($txt);

    if (strlen($txt) > 2) {
        if (preg_match("/^[a-zA-Z ]*$/",$txt) || isRTL($txt)) {
            return $txt;
        }
    } else {
        return false;
    }
}
function isNumber($number)
{
    $number = faTOen($number);
    if (strlen($number) > 4 && strlen($number) < 15 && preg_match('/^[0-9]{5,}$/', $number)) {
        return faTOen($number);
    } else {
        return false;
    }
}

function isRequest($req)
{
    if (!empty(substr($req, 0, strpos($req, "?")))) {
        $req = substr($req, 0, strpos($req, "?"));
    }
    //echo $req;
    return $req;
}
function isRTL($string)
{
    if (preg_match('/^[^\x{600}-\x{6FF}]+$/u', str_replace("\\\\", "", $string))) {
        return false;
    }
    return true;
}


/*
function createToken($data = array()) {

    $payload = array(
        $data,
    );
    
    $jwt = JWT::encode($payload, SECRET_KEY);
    
    
    return $jwt;
}


function checkToken($method = 'raw') {

    if ($method == 'raw') {
        $data = json_decode(file_get_contents("php://input"));
        $jwt = isset($data->jwt) ? $data->jwt : "";
        if($jwt){
    
            try{
                $decoded = JWT::decode($jwt, SECRET_KEY ,array('HS256'));
                return true;
                
            }
            catch(Exception $e){
                return false;
            }
        
        }else{
            return false;
        }
    } elseif($method == 'form_data') {
        $jwt = isset($_POST['_token']) ? $_POST['_token'] : "";
        if (empty($jwt)) {
            return false;
        }
        if($jwt){
    
            try{
                $decoded = JWT::decode($jwt, SECRET_KEY ,array('HS256'));
                return true;
                
            }
            catch(Exception $e){
                return false;
            }
        
        }else{
            return false;
        }
    } else {
        return false;
    }
}
*/


function checkExistenceTable($row_count = false) {
    if ($row_count !== false) {

        switch ($row_count) {
            case 0:
                return false;
                break;
            case 1:
                return true;
                break;
            case 1 < $row_count:
                return true;
                break;
        }

    } else {
        return false;
    }
} 


function clientIpAddress() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip_addr = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip_addr = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        $ip_addr = $_SERVER['REMOTE_ADDR'];
    }

    return $ip_addr;
}

/*
function decodeToken($_token) {
    try {
        if (!empty($_token)) {
            $decoded = JWT::decode($_token, SECRET_KEY ,array('HS256'));
    
            return $decoded;
        } else {
            return false;
        }
    
    } catch (Exception $e) {
        return false;
    }

    
}
*/
function validForDB($data , $no_space = false)
{

    $data = trim($data);
    $data = stripslashes($data);
    $data = strip_tags($data);
    $data = htmlspecialchars($data);
    if ($no_space) {
        $data = filter_var($data, FILTER_SANITIZE_URL);
    }
    return $data;    


}


function SMS2($number , $text , $flash = false)
{

    ini_set("soap.wsdl_cache_enabled", "0");
    $sms_client = new SoapClient('http://api.payamak-panel.com/post/send.asmx?wsdl', array('encoding'=>'UTF-8'));

    $parameters['username'] = SMS_USERNAME;
    $parameters['password'] = SMS_PASSWORD;
    $parameters['to'] = $number;
    $parameters['from'] = SMS_NUMBER;
    $parameters['text'] = $text;
    $parameters['isflash'] = $flash;

    return $sms_client->SendSimpleSMS($parameters)->SendSimpleSMS2Result;

}
function SMS1($number , $body_id , $args = [])
{
    $url = 'https://console.melipayamak.com/api/send/shared/5034b821c81a4329a6ac45d38128fe83';
    $data = array('bodyId' => $body_id, 'to' => $number, 'args' => $args);
    $data_string = json_encode($data);
    $ch = curl_init($url);                          
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");                      
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
    
    // Next line makes the request absolute insecure
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    // Use it when you have trouble installing local issuer certificate
    // See https://stackoverflow.com/a/31830614/1743997
    
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER,
      array('Content-Type: application/json',
            'Content-Length: ' . strlen($data_string))
      );
    try {
        $result = curl_exec($ch);
        return $result;
    } catch (\Throwable $e) {
        if(curl_errno($ch)){
            return 'Curl error: ' . curl_error($ch);
       }
    }
    curl_close($ch);
}
function sendSMS($number , $text_or_body_id , $args = [] , $flash = false)
{
    $SMS1 = SMS1($number , $text_or_body_id , $args);

    if ($SMS1) {

        return $SMS1;

    } else {
        return false;
    }


}
function shamsiTimeStamp($format = "Y-m-d h:s a")
{
    return jdate($format);
}

/*
function sendEmail($from , $to , $subject , $body , $to_title , $from_title)
{
    $mail = new PHPMailer(true);                              
    try {
        $mail->isSMTP(); // using SMTP protocol                                     
        $mail->Host = 'mail.neelabook.ir'; // SMTP host as gmail 
        $mail->SMTPAuth = true;  // enable smtp authentication                             
        $mail->Username = SMTP_MAIL;  // sender gmail host              
        $mail->Password = SMTP_PASSWORD; // sender gmail host password                          
        $mail->SMTPSecure = 'tls';  // for encrypted connection                           
        $mail->Port = 587;   // port for SMTP     
        $mail->CharSet = 'UTF-8';

        $mail->setFrom($from, $from_title); // sender's email and name
        $mail->addAddress($to, $to_title);  // receiver's email and name

        $mail->Subject = $subject;
        $mail->Body    = $body;

        $mail->send();
        
        return true;
    } catch (Exception $e) { // handle error.
        return 'Message could not be sent. Mailer Error: '. $mail->ErrorInfo;
    }
}
*/
function apiUrl()
{
    $actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    $actual_link = pathinfo($actual_link);
    $actual_link = pathinfo($actual_link['dirname'])['dirname'];
    $actual_link = $actual_link . "/";

    return $actual_link;
}
function websiteUrl()
{
    return "https://neelabook.ir/";
}
function faTOen($string) {
    return strtr($string, array('۰'=>'0', '۱'=>'1', '۲'=>'2', '۳'=>'3', '۴'=>'4', '۵'=>'5', '۶'=>'6', '۷'=>'7', '۸'=>'8', '۹'=>'9', '٠'=>'0', '١'=>'1', '٢'=>'2', '٣'=>'3', '٤'=>'4', '٥'=>'5', '٦'=>'6', '٧'=>'7', '٨'=>'8', '٩'=>'9'));
}