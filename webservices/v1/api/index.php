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


define('baseUrl', 'http://183.182.84.197/paravey/');
define('baseWebUrl', 'http://183.182.84.197/paravey/webservices/');
//define('baseUrl', 'http://knowledgeflow.in/shopin/webservices/');
// define('uploadPathShopImage', '../../images/shop_images/');
// define('shopImageUrl', 'http://knowledgeflow.in/shopin/webservices/images/shop_images/');
// define('uploadProductImage', '../../images/product_images/');
// define('productImageUrl', 'http://knowledgeflow.in/shopin/webservices/images/product_images/');
// define('categoryImageUrl', 'http://knowledgeflow.in/shopin/webservices/images/category_images/');
// define('promotionsImageUrl', 'http://knowledgeflow.in/shopin/webservices/images/promotions/');

\Slim\Slim::registerAutoloader();

$app = new \Slim\Slim();




/* API for LOGIN
 * URL : http://knowledgeflow.in/shopin/webservices/v1/api/login
  Method : Post
  Params : email, password
 * 
 * 
 */



$app->POST('/login', function() use ($app) {
   /* $email = $app->request->post('email'); 
    $password = md5($app->request->post('password'));
    $type = $app->request->post('type');
    $role_id = $app->request->post('role_id');
    $firstname = $app->request->post('first_name');
    $lastname = $app->request->post('last_name');
    $email = $app->request->post('email');
    $mobile = $app->request->post('mobile');
    $gender = $app->request->post('gender');
    $dob = $app->request->post('dob');*/
   // $deviceToken = $app->request->post('device_token');


    $jsonreq = $app->request->getBody();
    $encodedata = json_decode($jsonreq, true);
    
    $password = '';
    $mobile = '';
    $gender = '';
    $dob = '';
    $firstname = '';
    $lastname = '';
    $email = '';
    if(isset($encodedata['email'])){
      $email = $encodedata['email']; }
    if(isset($encodedata['password'])){
      $password = md5($encodedata['password']);}
    if(isset($encodedata['first_name'])){
      $firstname = $encodedata['first_name'];}
    if(isset($encodedata['last_name'])){  
      $lastname = $encodedata['last_name'];}
    if(isset($encodedata['mobile'])){
      $mobile = $encodedata['mobile'];}
    if(isset($encodedata['gender'])){  
      $gender = $encodedata['gender'];}
    if(isset($encodedata['dob'])){
       $dob = $encodedata['dob'];}
    $type = $encodedata['type'];
    $role_id = $encodedata['role_id'];

    try {
        $obj = new stdClass();
        $db = getDB();
       
        if($type == 2 || $type == 3 ){  // if first time sign in from fb or twitter (For Fb Type = 2 , For Twitter Type = 3 )
           $sql1 = "SELECT * FROM users where email='" . $email . "'";
           $stmt1 = $db->query($sql1);
           if ($stmt1->rowCount() == 0) { //If email is not registerd in db
                $sqlrole = "SELECT * FROM roles where id='" . $role_id . "'"; 
                $stmtrole = $db->query($sqlrole);  
                $roles = $stmtrole->fetch(PDO::FETCH_OBJ);

                $query = $db->query("insert into users(`role_id`,`first_name`,`last_name`,`email`,`mobile`,`gender`,`dob`,`type`,`is_verified`,`user_role`,`created_at`) "
                        . "values('". $role_id . "','" . $firstname . "','" . $lastname . "','" . $email . "','" . $mobile . "','" . $gender . "','" . $dob . "','" . $type . "',1,'" . $roles->name . "','" . date('Y-m-d H:i:s') . "')");
                 $userId = $db->lastInsertId();
                 $randNumber = big_rand(5); // generate new password for twitter and fb user to login from normal form
                
                // updating new password in DB
                 $db->query("update users set `password`='" . md5($randNumber) . "' where id=" . $userId); //update password for newly registered user from fb or twitter

                 $subject = 'New password has been created';
                 $body = 'Hello, ' . $firstname .' '. $lastname . ',<br><br>Your login credentials are as follows :<br><br>Email : ' . $email . '<br>Password :' . $randNumber;

                //////////////Email Sending////////////////////
                 $isSend = sendEmail($email, $subject, $body, '', '', ''); //send email to send new generated password

                 if (!$isSend) {
                      $json['status'] = 'error';
                      $json['key'] ='Email sending error due to SMTP problem';
                      $json['message'] = 'Sorry, due to some problem we are not able to send newly generated password.';
                      $json['data'] = $obj;
                      echo json_encode($json);
                 } else {
                      //$db = null;
                      $json['status'] = 'success';
                      $json['key'] = 'success';
                      $json['message'] = 'Logged in successfully. Your newly generated password is successfully sent on your registered email. ';
                      
                     
                 }
                 //////////////Email Sending////////////////////
                
                $sql2 = "SELECT * FROM users where email='" . $email . "'";
                $stmt2 = $db->query($sql2);
                if ($stmt2->rowCount() > 0) { 
                   $users = $stmt2->fetch(PDO::FETCH_OBJ);
                   $db = null;
                   
                   $json['data'] = array('userid' => $users->id, 'firstname' => $users->first_name,'lastname' => $users->last_name,'user_role' => $users->role_id, 'email' => $users->email, 'mobile' => $users->mobile, 'state' => $users->state, 'city' => $users->city, 'address' => $users->address, 'zip' => $users->zip,'gender' => $users->gender,'dob' => $users->dob);
                 
                   echo json_encode($json);
                 }
           }else{  // If email is registerd and login from fb or twitter 
                $sql = "SELECT * FROM users where email='" . $email . "' and type='" . $type . "' and is_verified = 1 and is_deleted = 0 and is_blocked = 0";
                $stmt = $db->query($sql);
                if ($stmt->rowCount() > 0) { 
                  $users = $stmt->fetch(PDO::FETCH_OBJ);
                  $db = null;
                  $json['status'] = 'success';
                  $json['key'] = 'success';
                  $json['message'] = 'Logged in successfully.';
             
                  $json['data'] = array('userid' => $users->id, 'firstname' => $users->first_name,'lastname' => $users->last_name,'user_role' => $users->role_id, 'email' => $users->email, 'mobile' => $users->mobile, 'state' => $users->state, 'city' => $users->city, 'address' => $users->address,  'zip' => $users->zip);
                  echo json_encode($json);
         
                  } else {
                    $sql2 = "SELECT * FROM users where email='" . $email . "' and type='" . $type . "'";
                    $stmt2 = $db->query($sql2);
                    if ($stmt2->rowCount() > 0) { 
                       $users = $stmt2->fetch(PDO::FETCH_OBJ);
                       if($users->is_deleted == 1){
                          $obj = new stdClass();
                          $json['status'] = 'error';
                          $json['key'] = 'Record is deleted from db';
                          $json['message'] = 'Invalid Email';
                          $json['data'] = $obj;
                          echo json_encode($json);
                       }else if($users->is_blocked == 1){
                          $obj = new stdClass();
                          $json['status'] = 'error';
                          $json['key'] = 'User is blocked';
                          $json['message'] = 'Invalid Email';
                          $json['data'] = $obj;
                          echo json_encode($json);
                       }
                    }else{
                      $obj = new stdClass();
                      $json['status'] = 'error';
                      $json['key'] = 'Email is not registerd';
                      $json['message'] = 'Invalid Email';
                      $json['data'] = $obj;
                      echo json_encode($json);
                    }
                  }
           } 
         }else{ // If login from normal form then check email and password is valid or not
          $sql = "SELECT * FROM users where email='" . $email . "' and password='" . $password . "' and is_verified = 1 and is_blocked = 0 and is_deleted = 0";
          $stmt = $db->query($sql); 

          if ($stmt->rowCount() > 0) {  // if email and password is valid
                $users = $stmt->fetch(PDO::FETCH_OBJ);
                $db = null;
                $json['status'] = 'success';
                $json['key'] = 'success';
                $json['message'] = 'Logged in successfully.';
                $json['data'] = array('userid' => $users->id, 'firstname' => $users->first_name,'lastname' => $users->last_name,'user_role' => $users->role_id, 'email' => $users->email, 'mobile' => $users->mobile, 'state' => $users->state, 'city' => $users->city, 'address' => $users->address, 'zip' => $users->zip);
                echo json_encode($json);
            
         
          } else {
              $sql1 = "SELECT * FROM users where email='" . $email . "' and password='" . $password . "'"; // check email is verified or not
              $stmt1 = $db->query($sql1); 
              if ($stmt1->rowCount() > 0) { // if not then send error message
                $users = $stmt1->fetch(PDO::FETCH_OBJ);
                if($users->is_deleted == 1){
                  $db = null;
                  $json['status'] = 'error';
                  $json['key'] = 'Record is deleted from db';
                  $json['message'] = 'Invalid Email';
                  echo json_encode($json);
                }else if($users->is_verified == 0){
                   $obj = new stdClass();
                   $json['status'] = 'error';
                   $json['key'] = 'Email is not verified'; 
                   $json['message'] = 'Your email is not verified';
                   $json['data'] = $obj;
                   echo json_encode($json);
                }else if($users->is_blocked == 1){
                   $obj = new stdClass();
                   $json['status'] = 'error';
                   $json['key'] = 'User is blocked';
                   $json['message'] = 'Invalid Email.';
                   $json['data'] = $obj;
                   echo json_encode($json);
                 }
              } else{
                $obj = new stdClass();
                $json['status'] = 'error';
                $json['key'] = 'Email or password is not correct';
                $json['message'] = 'Invalid Email / Password.';
                $json['data'] = $obj;
                echo json_encode($json);
            }
          }
      }
    } catch (PDOException $e) {
        //error_log($e->getMessage(), 3, '/var/tmp/php.log');
        echo '{"error":{"text":' . $e->getMessage() . '}}';
    }
});



/* API for registration 
 * URL : http://knowledgeflow.in/shopin/webservices/v1/api/register
 * PARAMS : user_role(2 for user, 3 for seller),fullname, email, mobile, password ,address,city,zipcode,device_token
 */
$app->post('/register', function() use ($app) {
  
    // $profilePhoto = $app->request->post('profile_photo');
   /* $role_id = $app->request->post('role_id');
    $firstname = $app->request->post('first_name');
    $lastname = $app->request->post('last_name');
    $email = $app->request->post('email');
    $mobile = $app->request->post('mobile');
    $password = md5($app->request->post('password'));
    $address = $app->request->post('address');
    $state = $app->request->post('state');
    $city = $app->request->post('city');
    $zip = $app->request->post('zip');
    $gender = $app->request->post('gender');
    $dob = $app->request->post('dob');*/

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
            $sql1 = "SELECT * FROM roles where id='" . $role_id . "'"; 
            $stmt1 = $db->query($sql1);  
            $roles = $stmt1->fetch(PDO::FETCH_OBJ);       
            $query = $db->query("insert into users(`role_id`,`first_name`,`last_name`,`email`,`mobile`,`password`,`address`,`city`,`state`,`zip`,`gender`,`dob`,`type`,`user_role`,`created_at`) "
                    . "values('". $role_id . "','" . $firstname . "','" . $lastname . "','" . $email . "','" . $mobile . "','" . $password . "','" . $address . "','" . $city . "','" . $state . "','" . $zip . "','" . $gender . "','" . $dob . "',1,'" . $roles->name . "','" . date('Y-m-d H:i:s') . "')");
            $userId = $db->lastInsertId();
           if($userId){
            $pointsRes = updatePoints($userId);
            $revid = reverse_string($userId);
            $revemail = reverse_string($email);  

            $encodeid = encode_string($revid);
            $encodeemail = encode_string($revemail);

            
            $subject = 'Email Verification';
            $body = 'Hello, ' . $firstname .' '. $lastname . '<br><br>You are successfully registerd. <br><br><a href="'.baseUrl.'backend/verification/'.$encodeid.'/'.$encodeemail.'">Click here to verifiy your email.</a>' ;

            //////////////Email Sending////////////////////
            $isSend = sendEmail($email, $subject, $body, '', '', '');

            if (!$isSend) {
                 $json['status'] = 'error';
                 $json['key'] = 'Email sending error due to SMTP problem';
                 $json['message'] = 'Sorry, due to some problem we are not able to send email verification link.';
                 $json['data'] = $obj;
                 echo json_encode($json);
            } else {
                 $db = null;
                 $json['status'] = 'success';
                 $json['key'] = 'success';
                 $json['message'] = 'You are successfully registered. Email verification link is successfully sent to your registered email.';
                 $json['data'] = array('userid' => $userId, 'firstname' => $firstname,'lastname' => $lastname,'user_role' => $role_id, 'email' => $email, 'mobile' =>
                 $mobile, 'state' => $state, 'city' => $city, 'address' => $address, 'zip' => $zip, 'dob' => $dob, 'gender'=>$gender);
                 echo json_encode($json);
            }
            //////////////Email Sending//////////////////// 
               /* $db = null;
                 $json['status'] = 'success';
                 $json['key'] = 'success';
                 $json['message'] = 'You are successfully registered.';
                 $json['data'] = array('userid' => $userId, 'firstname' => $firstname,'lastname' => $lastname,'user_role' => $role_id, 'email' => $email, 'mobile' =>
                 $mobile, 'state' => $state, 'city' => $city, 'address' => $address, 'zip' => $zip, 'dob' => $dob, 'gender'=>$gender);
                 echo json_encode($json);*/
            }else{
               $json['status'] = 'error';
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


    if(isset($encodedata['question'])){
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


$app->GET('/states', function() use ($app) {
    try {
      $obj = new stdClass();
      $db = getDB();
      $sql = "SELECT * FROM states where status=1";
      $stmt = $db->query($sql);
      $states = $stmt->fetchAll(PDO::FETCH_ASSOC);
        for($i=0;$i<count($states);$i++){
            $data[$i]['state_id'] = $states[$i]['id'];
            $data[$i]['state_name'] = $states[$i]['name'];
            $data[$i]['abbriviation'] = $states[$i]['shortname'];
         }
         $json['status'] = 'success';
         $json['key'] = 'success';
         $json['data'] = $data;
         echo json_encode($json);
       
      } catch (PDOException $e) {
        //error_log($e->getMessage(), 3, '/var/tmp/php.log');
        echo '{"error":{"text":' . $e->getMessage() . '}}';
     }
      
});

$app->GET('/cities/:stateid', function($state_id) use ($app) {
    try {
      $obj = new stdClass();
      $db = getDB();
      $sql = "SELECT * FROM cities where state_id= '".$state_id."' and status=1";
      $stmt = $db->query($sql);
      $cities = $stmt->fetchAll(PDO::FETCH_ASSOC);
        for($i=0;$i<count($cities);$i++){
            $data[$i]['city_id'] = $cities[$i]['id'];
            $data[$i]['city_name'] = $cities[$i]['name'];
            $data[$i]['state_id'] = $cities[$i]['state_id'];
         }
         $json['status'] = 'success';
         $json['key'] = 'success';
         $json['data'] = $data;
         echo json_encode($json);
       
      } catch (PDOException $e) {
        //error_log($e->getMessage(), 3, '/var/tmp/php.log');
        echo '{"error":{"text":' . $e->getMessage() . '}}';
     }
      
});


$app->run();