<?php

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS');
//header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token, Accept');
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
header('Access-Control-Allow-Credentials: true');

include 'db.php';
require 'Slim/Slim.php';
require 'phpmailer/PHPMailerAutoload.php';
include 'myfunction.php';


define('baseUrl', 'http://localhost/paravey-pre/');
define('baseWebUrl', 'http://localhost/paravey-pre/webservices/');
//define('baseUrl', 'http://knowledgeflow.in/shopin/webservices/');
// define('uploadPathShopImage', '../../images/shop_images/');
// define('shopImageUrl', 'http://knowledgeflow.in/shopin/webservices/images/shop_images/');
// define('uploadProductImage', '../../images/product_images/');
// define('productImageUrl', 'http://knowledgeflow.in/shopin/webservices/images/product_images/');
// define('categoryImageUrl', 'http://knowledgeflow.in/shopin/webservices/images/category_images/');
// define('promotionsImageUrl', 'http://knowledgeflow.in/shopin/webservices/images/promotions/');

\Slim\Slim::registerAutoloader();

$app = new \Slim\Slim();





/* API for registration 
 * URL : http://knowledgeflow.in/shopin/webservices/v1/api/register
 * PARAMS : user_role(2 for user, 3 for seller),fullname, email, mobile, password ,address,city,zipcode,device_token
 */
$app->post('/register', function() use ($app) {
  
    $password = '';
    $mobile = '';
    $gender = '';
    $dob = '';
    $firstname = '';
    $lastname = '';
    $email = '';
    $address = '';
    $state = '';
    $city = '';
    $zip = '';

    $jsonreq = $app->request->getBody();
    $encodedata = json_decode($jsonreq, true);


    if(isset($encodedata['email'])){
       $email = $encodedata['email']; }
    if(isset($encodedata['password'])){
      $password = md5($encodedata['password']);}
    if(isset($encodedata['first_name'])){
      $firstname = $encodedata['first_name']; }
    if(isset($encodedata['last_name'])){  
      $lastname = $encodedata['last_name']; }
    if(isset($encodedata['mobile'])){
      $mobile = $encodedata['mobile']; }
    if(isset($encodedata['gender'])){  
      $gender = $encodedata['gender']; }
    if(isset($encodedata['dob'])){
       $dob = $encodedata['dob']; }
    if(isset($encodedata['address'])){  
      $address = $encodedata['address']; }
    if(isset($encodedata['state'])){
      $state = $encodedata['state']; }
    if(isset($encodedata['city'])){  
      $city = $encodedata['city']; }
    if(isset($encodedata['zip'])){
      $zip = $encodedata['zip']; }   
    $role_id = $encodedata['role_id'];
   
    try {
        $db = getDB();
        $sql = "SELECT * FROM users where email='" . $email . "'"; 
        $stmt = $db->query($sql);
        $obj = new stdClass();  
        if ($stmt->rowCount() > 0) {
            $obj = new stdClass();
            $json['status'] = 'error';
            $json['key'] ='Email exist in db';
            $json['message'] = 'Email already exist. Please use other email.';
            $json['data'] = $obj;
            echo json_encode($json);
        } else {       
            $query = $db->query("insert into users(`role_id`,`first_name`,`last_name`,`email`,`mobile`,`password`,`address`,`city`,`state`,`zip`,`gender`,`dob`,`type`,`created_at`) "
                    . "values('". $role_id . "','" . $firstname . "','" . $lastname . "','" . $email . "','" . $mobile . "','" . $password . "','" . $address . "','" . $city . "','" . $state . "','" . $zip . "','" . $gender . "','" . $dob . "',1,'" . date('Y-m-d H:i:s') . "')");
            $userId = $db->lastInsertId();
             if($userId){
              $revid = reverse_string($userId);
              $revemail = reverse_string($email);  

              $encodeid = encode_string($revid);
              $encodeemail = encode_string($revemail);

            
                  // $subject = 'Email Verification';
                  // $body = 'Hello, ' . $firstname .' '. $lastname . '<br><br>You are successfully registerd. <br><br><a href="'.baseUrl.'backend/verification/'.$encodeid.'/'.$encodeemail.'">Click here to verifiy your email.</a>' ;

                  // //////////////Email Sending////////////////////
                  // $isSend = sendEmail($email, $subject, $body, '', '', '');

                  // if (!$isSend) {
                  //      $json['status'] = 'error';
                  //      $json['key'] = 'Email sending error due to SMTP problem';
                  //      $json['message'] = 'Sorry, due to some problem we are not able to send email verification link.';
                  //      $json['data'] = $obj;
                  //      echo json_encode($json);
                  // } else {
                  //      $db = null;
                  //      $json['status'] = 'success';
                  //      $json['key'] = 'success';
                  //      $json['message'] = 'You are successfully registered. Email verification link is successfully sent to your registered email.';
                  //      $json['data'] = array('userid' => $userId, 'firstname' => $firstname,'lastname' => $lastname,'user_role' => $role_id, 'email' => $email, 'mobile' =>
                  //      $mobile, 'state' => $state, 'city' => $city, 'address' => $address, 'zip' => $zip, 'dob' => $dob, 'gender'=>$gender);
                  //      echo json_encode($json);
                  // }
                  //////////////Email Sending////////////////////  

             $db = null;
                 $json['status'] = 'success';
                 $json['key'] = 'success';
                 $json['message'] = 'You are successfully registered.';
                 $json['data'] = array('userid' => $userId, 'firstname' => $firstname,'lastname' => $lastname,'user_role' => $role_id, 'email' => $email, 'mobile' =>
                 $mobile, 'state' => $state, 'city' => $city, 'address' => $address, 'zip' => $zip, 'dob' => $dob, 'gender'=>$gender);
                 echo json_encode($json);
                     
            }else{
               $json['status'] = 'error';
               $json['key'] = 'Internal server error';
               $json['message'] = 'Sorry, due to some problem you are not successfully registerd.';
               $json['data'] = $obj;
               echo json_encode($json);
            }
        }
    } catch (PDOException $e) {
        //error_log($e->getMessage(), 3, '/var/tmp/php.log');
        echo '{"error":{"text":' . $e->getMessage() . '}}';
    }
});


$app->post('/quiz', function() use ($app) {
  
   
    $ques = '';
    $answer = '';
    $userid = '';
    $jsonreq = $app->request->getBody();
    $encodedata = json_decode($jsonreq, true);


    if(isset($encodedata['quesion'])){
       $ques = $encodedata['question']; }
    if(isset($encodedata['answer'])){
      $answer = $encodedata['answer']; }
    if(isset($encodedata['user_id'])){
      $userid = $encodedata['user_id']; }  
   
    try {
           $db = getDB();
            
            $query = $db->query("insert into quiz_record(`role_id`,`question`,`answer`,`user_id`) "
                    . "values(4,'" . $ques . "','" . $answer . "','" . $userid . "')");
            $quizid = $db->lastInsertId();
           if($quizid){
              $db = null;
              $json['status'] = 'success';
              $json['key'] = 'success';
              $json['message'] = 'Quiz is submitted';
              $json['data'] = array('quizid' => $quizid);
              echo json_encode($json);
            }else{
               $json['status'] = 'error';
               $json['key'] = 'Internal server error';
               $json['message'] = 'Sorry, due to some problem quiz is not submitted.';
               $json['data'] = $obj;
               echo json_encode($json);
            }
      
    } catch (PDOException $e) {
        //error_log($e->getMessage(), 3, '/var/tmp/php.log');
        echo '{"error":{"text":' . $e->getMessage() . '}}';
    }
});

$app->run();