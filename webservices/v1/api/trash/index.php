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

$app->POST('/testing', function() use ($app) {
   $app->response->headers->set('Access-Control-Allow-Origin', '*');
   $app->response->headers->set('Content-Type', 'application/x-www-form-urlencoded');
   echo 1;
      
});

$app->POST('/login', function() use ($app) {
    $email = $app->request->post('email'); 
    $password = md5($app->request->post('password'));
    $type = $app->request->post('type');
    $role_id = $app->request->post('role_id');
    $firstname = $app->request->post('first_name');
    $lastname = $app->request->post('last_name');
    $email = $app->request->post('email');
    $mobile = $app->request->post('mobile');
    $gender = $app->request->post('gender');
    $dob = $app->request->post('dob');
   // $deviceToken = $app->request->post('device_token');

    try {
        $obj = new stdClass();
        $db = getDB();
       
        if($type == 2 || $type == 3 ){  // if first time sign in from fb or twitter (For Fb Type = 2 , For Twitter Type = 3 )
           $sql1 = "SELECT * FROM users where email='" . $email . "'";
           $stmt1 = $db->query($sql1);
           if ($stmt1->rowCount() == 0) { //If email is not registerd in db
                $query = $db->query("insert into users(`role_id`,`first_name`,`last_name`,`email`,`mobile`,`gender`,`dob`,`type`,`status`,`created_at`) "
                        . "values('". $role_id . "','" . $firstname . "','" . $lastname . "','" . $email . "','" . $mobile . "','" . $gender . "','" . $dob . "','" . $type . "',1,'" . date('Y-m-d H:i:s') . "')");
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
                $sql = "SELECT * FROM users where email='" . $email . "' and type='" . $type . "' and status = 1";
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
                      $obj = new stdClass();
                      $json['status'] = 'error';
                      $json['key'] = 'Email is not registerd';
                      $json['message'] = 'Invalid Email.';
                      $json['data'] = $obj;
                      echo json_encode($json);
                  }
           } 
         }else{ // If login from normal form then check email and password is valid or not
          $sql = "SELECT * FROM users where email='" . $email . "' and password='" . $password . "'";
          $stmt = $db->query($sql); 

          if ($stmt->rowCount() > 0) {  // if email and password is valid
              $sql1 = "SELECT * FROM users where email='" . $email . "' and password='" . $password . "' and status = 1"; // check email is verified or not
              $stmt1 = $db->query($sql); 
              if ($stmt->rowCount() == 0) { // if not then send error message
                $db = null;
                $json['status'] = 'error';
                $json['key'] = 'Email is not verified';
                $json['message'] = 'Your email is not verified.';
                echo json_encode($json);
              } else{
                $users = $stmt->fetch(PDO::FETCH_OBJ);
                $db = null;
                $json['status'] = 'success';
                $json['key'] = 'success';
                $json['message'] = 'Logged in successfully.';
                $json['data'] = array('userid' => $users->id, 'firstname' => $users->first_name,'lastname' => $users->last_name,'user_role' => $users->role_id, 'email' => $users->email, 'mobile' => $users->mobile, 'state' => $users->state, 'city' => $users->city, 'address' => $users->address, 'zip' => $users->zip);
                echo json_encode($json);
            }
         
          } else {
              $obj = new stdClass();
              $json['status'] = 'error';
              $json['key'] = 'Email or password is not correct';
              $json['message'] = 'Invalid Email / Password.';
              $json['data'] = $obj;
              echo json_encode($json);
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
    $role_id = $app->request->post('role_id');
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
    $dob = $app->request->post('dob');
    
   
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

            
            $subject = 'Email Verification';
            $body = 'Hello, ' . $firstname .' '. $lastname . '<br><br>You are successfully registerd. <br><br><a href="'.baseUrl.'verification/'.$encodeid.'/'.$encodeemail.'">Click here to verifiy your email.</a>' ;

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


/* API for Forgot password
 * 
 * 
 * 
 */
$app->post('/forgot_password', function() use ($app) {

    $email = $app->request->post('email');
    try {
        $db = getDB();
        $sql = "SELECT * FROM user_detail where email='" . $email . "'";
        $stmt = $db->query($sql);
        if ($stmt->rowCount() <= 0) {
            $obj = new stdClass();
            $json['status'] = 'error';
            $json['message'] = 'This Email does not exist in database.';
            //$json['data'] = $obj;
            echo json_encode($json);
        } else {
            $userData = $stmt->fetch(PDO::FETCH_ASSOC);
            $name = $userData['fullname'];
            $name = ucwords(strtolower($name));
            $randNumber = $this->big_rand(5);
            //print_r($userData);
            //exit;
            // updating new password in DB
            $db->query("update user_detail set `password`='" . md5($randNumber) . "' where ud_id=" . $userData->ud_id);

            $subject = 'Password Reset Shopin';
            $body = 'Hello ' . $name . ',<br><br>Your login credentials are as follows :<br><br>Email : ' . $userData['email'] . '<br>Password :' . $randNumber;


            $isSend = sendEmail($email, $subject, $body, '', '', '');

            if (!$isSend) {

                $json['status'] = 'error';
                $json['message'] = 'Sorry, due to some problem we are not able to send your password.';
                //$json['data'] = array('userid'=>$userId,'name'=>$firstName.' '.$lastName);
                echo json_encode($json);
            } else {

                $db = null;
                $json['status'] = 'success';
                $json['message'] = 'Your password is successfully send on your registered email.';
                //$json['data'] = array('userid'=>$userId,'name'=>$firstName.' '.$lastName);
                echo json_encode($json);
            }
        }
    } catch (PDOException $e) {
        //error_log($e->getMessage(), 3, '/var/tmp/php.log');
        echo '{"error":{"text":' . $e->getMessage() . '}}';
    }
});

$app->get('/languages', function() use ($app) {

    try {
        $db = getDB();
        $query = "SELECT lang_id,lang_name FROM language where lang_status=1 and lang_deleted=0";
        $stmt = $db->query($query);
        if ($stmt->rowCount() > 0) {
            $langData = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $db = null;
            $json['status'] = 'success';
            $json['message'] = 'Listing ' . $stmt->rowCount() . ' record(s).';
            $json['data'] = $langData;
            echo json_encode($json);
        } else {
            $obj = new stdClass();
            $json['status'] = 'success';
            $json['message'] = 'No record found.';
            $json['data'] = $obj;
            echo json_encode($json);
        }
    } catch (PDOException $e) {
        //error_log($e->getMessage(), 3, '/var/tmp/php.log');
        echo '{"error":{"text":' . $e->getMessage() . '}}';
    }
});

$app->get('/users/:userid',function($userid) use ($app){
    
    try {
            $db = getDB();
            $query = "select ud_id as userid,concat_ws(' ',ud_first_name,ud_last_name) as name,concat('".imageUrl."',ud_profile_photo) as photo, '0' as 'unread_count' "
                    . "from user_detail where ud_status=1 and ud_deleted=0 and fk_ur_id=2 and ud_id!=".$userid;
            $stmt = $db->query($query);  
            if($stmt->rowCount() > 0)
            {
                $userData = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $db = null;
                $json['status'] = 'success';
                $json['message'] = 'Listing '.$stmt->rowCount().' record(s).';
                $json['data'] = $userData;
                echo json_encode($json);
            }
            else
            {
                $obj = new stdClass();
                $json['status'] = 'success';
                $json['message'] = 'No record found.';
                $json['data'] = $obj;
                echo json_encode($json);
            }
    } catch(PDOException $e) {
        //error_log($e->getMessage(), 3, '/var/tmp/php.log');
            echo '{"error":{"text":'. $e->getMessage() .'}}'; 
    }
});

$app->post('/chat_notification',function() use ($app){
    
    $apiKey = 'AIzaSyAxXxVJd5Emm5KZgpA-fy0ra77MjR307YE';
    $senderId = $app->request->post('sender_id');
    $receiverId = $app->request->post('receiver_id');
    $chatMessage = $app->request->post('message');
    $timeStamp = $app->request->post('timestamp');
    $idArray = getDeviceTokenByUserId($receiverId);
    $registrationIds = array($idArray);
    $message = array('message'=>array('chat'=>$chatMessage,'sender_id'=>$senderId,'receiver_id'=>$receiverId,'timestamp'=>$timeStamp));
    sendAndroidPushNotification($apiKey,$registrationIds,$message);   
    
    $json['status'] = 'success';
    $json['message'] = 'Message send successfully.';
    $json['data'] = array('sender_id'=>$senderId,'receiver_id'=>$receiverId,'message'=>$chatMessage,'timestamp'=>$timeStamp);
    echo json_encode($json);
            
});

/* API for Add Product 
 * URL : http://knowledgeflow.in/shopin/webservices/v1/api/add_product
 * PARAMS : user_id,product_name, category_id, price, offer_price ,stock,city,size,image1,image2,image3
 */
$app->post('/add_product', function() use ($app) {

    // $profilePhoto = $app->request->post('profile_photo');
    $user_id = $app->request->post('user_id');
    $product_name = $app->request->post('product_name');
    
    $category_id = $app->request->post('category_id');
    $sku = $app->request->post('sku');
    $price = $app->request->post('price');
    $offer_price = $app->request->post('offer_price');
    $stock = $app->request->post('stock');
    $size = $app->request->post('size');
    $color = $app->request->post('color');
    $image1 = isset($_FILES['image1']) ? $_FILES['image1'] : NULL ;
    $image2 = isset($_FILES['image2']) ? $_FILES['image2'] : NULL ;
    $image3 = isset($_FILES['image3']) ? $_FILES['image3'] : NULL ;
    //$created_date = date('Y-m-d H:i:s');
    $status = 1;
    

    try {
        $db = getDB();

            $query = $db->query("insert into products(`user_id`,`product_name`,`category_id`,`sku`,`price`,`offer_price`,`stock`,`size`,`color`,`created_date`,`status`) "
                    . "values(" . $user_id . ",'" . $product_name . "','" . $category_id . "','" . $sku . "','" . $price . "','" . $offer_price . "','" . $stock . "','" . $size . "','" . $color . "','" . date('Y-m-d H:i:s') . "','" . $status . "')");
            $product_id = $db->lastInsertId();
            
              //code to insert product image1
            $imageNameArray = array();
              if($image1!="" || $image1!=NULL)
              {
              $fileName = uploadImage($user_id,$image1,uploadProductImage);

              $db->query("update products set image1 = '".$fileName."' where pid = ".$product_id);

              $imageNameArray['image1'] = uploadProductImage.$fileName;
              }
              else
              {
              $imageNameArray['image1'] = '';
              }
              
                            //code to insert product image2
              if($image2!="" || $image2!=NULL)
              {
              $fileName = uploadImage($user_id,$image2,uploadProductImage);

              $db->query("update products set image2 = '".$fileName."' where pid = ".$product_id);

              $imageNameArray['image2'] = uploadProductImage.$fileName;
              }
              else
              {
              $imageNameArray['image2'] = '';
              }
                            //code to insert product image3
            
              if($image3!="" || $image3!=NULL)
              {
              $fileName = uploadImage($user_id,$image3,uploadProductImage);

              $db->query("update products set image3 = '".$fileName."' where pid = ".$product_id);

              $imageNameArray['image3'] = uploadProductImage.$fileName;
              }
              else
              {
              $imageNameArray['image3'] = '';
              }
             
            $db = null;
            $json['status'] = 'success';
            $json['message'] = 'Successfully Added Product.';
            $json['data'] = array('product_id' => $product_id);
            echo json_encode($json);
        
    } catch (PDOException $e) {
        //error_log($e->getMessage(), 3, '/var/tmp/php.log');
        echo '{"error":{"text":' . $e->getMessage() . '}}';
    }
});




/* API for update Product 
 * URL : http://knowledgeflow.in/shopin/webservices/v1/api/update_product/integer(product_id)
 * PARAMS : user_id,product_name, category_id, price, offer_price ,stock,city,size,image1,image2,image3
 */
$app->post('/update_product/:product_id', function($product_id) use ($app) {

    // $profilePhoto = $app->request->post('profile_photo');
    $user_id = $app->request->post('user_id');
    $product_name = $app->request->post('product_name');
    $category_id = $app->request->post('category_id');
    $sku = $app->request->post('sku');
    $price = $app->request->post('price');
    $offer_price = $app->request->post('offer_price');
    $stock = $app->request->post('stock');
    $size = $app->request->post('size');
    $color = $app->request->post('color');
    $image1 = isset($_FILES['image1']) ? $_FILES['image1'] : NULL ;
    $image2 = isset($_FILES['image2']) ? $_FILES['image2'] : NULL ;
    $image3 = isset($_FILES['image3']) ? $_FILES['image3'] : NULL ;
    //$created_date = date('Y-m-d H:i:s');
    $status = 1;
    

    try {
        $db = getDB();

$query = $db->query("update products set `user_id` = $user_id ,`product_name`= '".$product_name."',`category_id` = '".$category_id."',`sku` = '".$sku."',`price`='".$price."',`offer_price`='".$offer_price."',`stock`='".$stock."',`size`='".$size."',`color`='".$color."',`image1`='".$image1."',`image2`='".$image2."',`image3`='".$image3."',`created_date`='".date('Y-m-d H:i:s')."',`status`=$status where `pid`=$product_id");
           
           //code to insert product image1
            $imageNameArray = array();
              if($image1!="" || $image1!=NULL)
              {
              $fileName = uploadImage($userId,$image1,uploadProductImage);

              $db->query("update products set image1 = '".$fileName."' where pid = ".$product_id);

              $imageNameArray['image1'] = uploadProductImage.$fileName;
              }
              else
              {
              $imageNameArray['image1'] = '';
              }
              
                            //code to insert product image2
              if($image2!="" || $image2!=NULL)
              {
              $fileName = uploadImage($userId,$image2,uploadProductImage);

              $db->query("update products set image2 = '".$fileName."' where pid = ".$product_id);

              $imageNameArray['image2'] = uploadProductImage.$fileName;
              }
              else
              {
              $imageNameArray['image2'] = '';
              }
                            //code to insert product image3
            
              if($image3!="" || $image3!=NULL)
              {
              $fileName = uploadImage($userId,$image3,uploadProductImage);

              $db->query("update products set image3 = '".$fileName."' where pid = ".$product_id);

              $imageNameArray['image3'] = uploadProductImage.$fileName;
              }
              else
              {
              $imageNameArray['image3'] = '';
              }
            $db = null;
            $json['status'] = 'success';
            $json['message'] = 'Successfully Updated Product.';
            $json['data'] = array('product_id' => $product_id);
            echo json_encode($json);
        
    } catch (PDOException $e) {
        //error_log($e->getMessage(), 3, '/var/tmp/php.log');
        echo '{"error":{"text":' . $e->getMessage() . '}}';
    }
});

/*API for list products
 * 
 * 
 * 
 */
$app->get('/productList/:user_id',function($userid) use ($app){
    
    try {
            $db = getDB();
            $query = "select pid,product_name,category_id,sku,price,offer_price,stock,size,color,concat('".productImageUrl."',image1) as image1,concat('".productImageUrl."',image2) as image2,concat('".productImageUrl."',image3) as image3 from products where user_id=".$userid." and status=1";
      
            $stmt = $db->query($query);  
            if($stmt->rowCount() > 0)
            {
                $productData = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $db = null;
                $json['status'] = 'success';
                $json['message'] = 'Total '.$stmt->rowCount().' record(s).';
                $json['data'] = $productData;
                echo json_encode($json);
            }
            else
            {
                $obj = array();
                $json['status'] = 'success';
                $json['message'] = 'No record found.';
                $json['data'] = $obj;
                echo json_encode($json);
            }
    } catch(PDOException $e) {
        //error_log($e->getMessage(), 3, '/var/tmp/php.log');
            echo '{"error":{"text":'. $e->getMessage() .'}}'; 
    }
});

/*API for delete products
 * 
 * 
 * 
 */
$app->get('/productDelete/:product_id',function($product_id) use ($app){
    
    try {
            $db = getDB();
            $query = "select * from products where pid=".$product_id;
      
            $stmt = $db->query($query);  
            if($stmt->rowCount() > 0)
            {
                $productData = $stmt->fetchAll(PDO::FETCH_ASSOC);
                 
               
                //Deleting product 
                $query = "Delete from products where pid=".$product_id;
                $stmt = $db->query($query);
                $db = null;

                //deletting product in=mages
                if($productData[0]['image1'])
                unlink(uploadProductImage.$productData[0]['image1']);
                
                if($productData[0]['image2'])
                unlink(uploadProductImage.$productData[0]['image2']);
                
                if($productData[0]['image3'])
                unlink(uploadProductImage.$productData[0]['image3']);
                
                $json['status'] = 'success';
                $json['message'] = 'Product Deleted';
                //$json['data'] = $productData;
                echo json_encode($json);
            }
            else
            {
                $obj = new stdClass();
                $json['status'] = 'success';
                $json['message'] = 'No record found.';
                $json['data'] = $obj;
                echo json_encode($json);
            }
    } catch(PDOException $e) {
        //error_log($e->getMessage(), 3, '/var/tmp/php.log');
            echo '{"error":{"text":'. $e->getMessage() .'}}'; 
    }
});


/*API to get My Account info
 * 
 * 
 * 
 */
$app->get('/myAccount/:userid',function($userid) use ($app){
    
    try {
            $db = getDB();
            $query = "select * from user_detail where ud_id=".$userid;
            $stmt = $db->query($query);  
            if($stmt->rowCount() > 0)
            {
                $userData = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $db = null;
                $json['status'] = 'success';
                $json['data'] = $userData;
                echo json_encode($json);
            }
            else
            {
                $obj = new stdClass();
                $json['status'] = 'success';
                $json['message'] = 'No record found.';
                $json['data'] = $obj;
                echo json_encode($json);
            }
    } catch(PDOException $e) {
        //error_log($e->getMessage(), 3, '/var/tmp/php.log');
            echo '{"error":{"text":'. $e->getMessage() .'}}'; 
    }
});

/*API for updating user info
 * 
 * 
 */
$app->get('/updateMyAccount/:userid',function($userid) use ($app){
    
    try {
           
    $fullname = $app->request->post('fullname');
    $email = $app->request->post('email');
    $mobile = $app->request->post('mobile');
    $password = md5($app->request->post('password'));
    $address = $app->request->post('address');
    $city = $app->request->post('city');
    $zipcode = $app->request->post('zipcode');
    $deviceToken = $app->request->post('device_token');
    $shop_name = $app->request->post('shop_name');
    $shop_image = $app->request->post('image');
        
        $db = getDB();
        $sql = "SELECT * FROM user_detail where email='" . $email . "' and `ud_id` !=".$userid;
        $stmt = $db->query($sql);
        if ($stmt->rowCount() > 0) {
            $obj = new stdClass();
            $json['status'] = 'error';
            $json['message'] = 'Email already exist in database. Please use other email.';
            $json['data'] = $obj;
            echo json_encode($json);
        } else {       
            $query = $db->query("update user_detail set `fullname` = '" . $fullname . "',`email` = '" . $email . "',`mobile` = '" . $mobile . "',`password` = '" . $password . "',`address` = '" . $address . "',`city` = '" . $city . "',`zipcode` = '" . $zipcode . "',`device_token` = '" . $deviceToken . "',`shop_name` = '" . $shop_name . "',`created` = '".date('Y-m-d H:i:s')."'");
           // $userId = $db->lastInsertId();
//code to insert user profile photo
            
            
              if($shop_image!="" || $shop_image!=NULL)
              {
              $fileName = uploadBase64Image($userid,$shop_image,uploadPathShopImage);

              $db->query("update user_detail set shop_image = '".$fileName."' where ud_id = ".$userid);

              $fileName = uploadPath.$fileName;
              }
              else
              {
              $fileName = '';
              }
             
             
                
            $db = null;
            $json['status'] = 'success';
            $json['message'] = 'Successfully registered.';
            $json['data'] = array('userid' => $userid, 'name' => $fullname);
            echo json_encode($json);
            }
    } catch(PDOException $e) {
        //error_log($e->getMessage(), 3, '/var/tmp/php.log');
            echo '{"error":{"text":'. $e->getMessage() .'}}'; 
    }
});


/* API for order
 * 
 * http://knowledgeflow.in/shopin/webservices/v1/api/newOrder/integer(user_id)
 */
$app->post('/newOrder/:userid', function($userid) use ($app) {

    try {

        $order_id = uniqid();
        $buyer_id = $userid;
        $seller_id = $app->request->post('seller_id');
        $product_id = $app->request->post('product_id');
        $payment_mode = $app->request->post('payment_mode'); //COD,CC
        
       /* $test = array('0' =>array('seller_id' => 25, 'product_id' => 201, 'payment_mode' => 'COD'),'1' =>array('seller_id' => 25, 'product_id' => 201, 'payment_mode' => 'CC'));
        echo json_encode($test);
        die; */
        $order_datetime = date('Y-m-d H:i:s');
        $status = 'ordered'; //ordered,processed,delivered,cancelled,refund

        $db = getDB();
        $sql = "SELECT * FROM products where pid=" . $product_id;
        $stmt = $db->query($sql);
        if ($stmt->rowCount() > 0) {

            $productData = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $product_name = $productData[0]['product_name'];
            $product_price = $productData[0]['price'];


            $query = $db->query("insert into transactions (`order_id`,`buyer_id`,`seller_id`,`product_id`,`product_name`,`product_price`,`payment_mode`,`order_datetime`,`status`) values('" . $order_id . "','" . $buyer_id . "','" . $seller_id . "','" . $product_id . "','" . $product_name . "','" . $product_price . "','" . $payment_mode . "','" . $order_datetime . "','" . $status . "')");


            //updating inventory
            $newStock = $productData[0]['stock'] - 1;
            $query = $db->query("update products set `stock` =" . $newStock . " where pid=" . $product_id);

            $obj = new stdClass();
            $json['status'] = 'success';
            $json['message'] = 'Your ordered has been successfully placed.';
            $json['data'] = array('order_id' => $order_id);
            echo json_encode($json);
        } else {

            $db = null;
            $json['status'] = 'error';
            $json['message'] = 'Product Not Found.';
            $json['data'] = array('seller_id' => $seller_id, 'product_id' => $product_id);
            echo json_encode($json);
        }
    } catch (PDOException $e) {
        //error_log($e->getMessage(), 3, '/var/tmp/php.log');
        echo '{"error":{"text":' . $e->getMessage() . '}}';
    }
});


/* API for getting all shops
 * 
 * http://knowledgeflow.in/shopin/webservices/v1/api/allShops
 * 
 */
$app->get('/allShops', function() use ($app) {


    try {
        $db = getDB();
        $sql = "SELECT * FROM user_detail where fk_ur_id =3";
        $stmt = $db->query($sql);
       
            $shopData = $stmt->fetchAll(PDO::FETCH_ASSOC);
            for($i=0;$i<count($shopData);$i++){
                
                $data[$i]['shop_id'] = $shopData[$i]['ud_id'];
                $data[$i]['shop_name'] = $shopData[$i]['shop_name'];
                if($shopData[$i]['shop_image']){
                   $data[$i]['shop_image'] = shopImageUrl.$shopData[$i]['shop_image']; 
                }else{
                    $data[$i]['shop_image'] = NULL;
                    
                }

                
            }
            $db = null;
            $json['status'] = 'success';
            $json['message'] = 'All Shops Lists.';
            $json['data'] = $data;
            echo json_encode($json);
            
    } catch (PDOException $e) {
        //error_log($e->getMessage(), 3, '/var/tmp/php.log');
        echo '{"error":{"text":' . $e->getMessage() . '}}';
    }
});


/* API for product list of a particular shop
 * 
 * URL : http://knowledgeflow.in/shopin/webservices/v1/api/listProducts/integer(category_id)
 * 
 */
$app->post('/listProducts', function() use ($app) {

    try {
        $category_id = $app->request->post('categoryId');
        $limit = $app->request->post('limit');
        $endLimit = $limit + 20;
        $db = getDB();
        $query = "select pid,product_name,user_id,category_id,price,offer_price,stock,size,color,image1,image2,image3 from products where category_id=" . $category_id . " and status=1 LIMIT $limit ,$endLimit";

        $stmt = $db->query($query);
        if ($stmt->rowCount() > 0) {
            $shopData = $stmt->fetchAll(PDO::FETCH_ASSOC);
            for ($i = 0; $i < count($shopData); $i++) {

                $data[$i]['product_id'] = $shopData[$i]['pid'];
                $data[$i]['product_name'] = $shopData[$i]['product_name'];
                $data[$i]['price'] = $shopData[$i]['price'];
                $data[$i]['seller_id'] = $shopData[$i]['user_id'];
                $data[$i]['category_id'] = $shopData[$i]['category_id'];
                $data[$i]['offer_price'] = $shopData[$i]['offer_price'];
                $data[$i]['stock'] = $shopData[$i]['stock'];
                $data[$i]['size'] = $shopData[$i]['size'];
                $data[$i]['color'] = $shopData[$i]['color'];


                if ($shopData[$i]['image1']) {
                    $data[$i]['image1'] = productImageUrl . $shopData[$i]['image1'];
                } else {
                    $data[$i]['image1'] = NULL;
                }


                if ($shopData[$i]['image2']) {
                    $data[$i]['image2'] = productImageUrl . $shopData[$i]['image2'];
                } else {
                    $data[$i]['image2'] = NULL;
                }


                if ($shopData[$i]['image3']) {
                    $data[$i]['image3'] = productImageUrl . $shopData[$i]['image3'];
                } else {
                    $data[$i]['image3'] = NULL;
                }
            }
            $db = null;
            $json['status'] = 'success';
            $json['message'] = 'All Product Lists.';
            $json['data'] = $data;
            echo json_encode($json);
        } else {
            $obj = new stdClass();
            $json['status'] = 'success';
            $json['message'] = 'No record found.';
            $json['data'] = $obj;
            echo json_encode($json);
        }
    } catch (PDOException $e) {
        //error_log($e->getMessage(), 3, '/var/tmp/php.log');
        echo '{"error":{"text":' . $e->getMessage() . '}}';
    }
});


/* API for getting all categories
 * 
 * http://knowledgeflow.in/shopin/webservices/v1/api/getCategories
 * 
 */
$app->get('/getCategories', function() use ($app) {


    try {
        $data = array();
        $db = getDB();
        
        //getting promotions images
        
        $sql = "SELECT * FROM promotions";
        $stmt = $db->query($sql);
        $promotionsData = $stmt->fetchAll(PDO::FETCH_ASSOC);
        for($i=0;$i<count($promotionsData);$i++){
            
            $image[$i] = promotionsImageUrl.$promotionsData[$i]['path'];
        }
        
        $sql = "SELECT * FROM categories";
        $stmt = $db->query($sql);
       
            $categoryData = $stmt->fetchAll(PDO::FETCH_ASSOC);
            for($i=0;$i<count($categoryData);$i++){
                
                $data[$i]['cat_id'] = $categoryData[$i]['id'];
                $data[$i]['cat_name'] = $categoryData[$i]['name'];
                $data[$i]['parent_id'] = $categoryData[$i]['parentid'];
                $data[$i]['path'] = categoryImageUrl.$categoryData[$i]['path'];

            }
            $db = null;
            $json['status'] = 'success';
            $json['message'] = 'All Categories Lists.';
            $json['data'] = $data;
            $json['image'] = $image;
            echo json_encode($json);
            
    } catch (PDOException $e) {
        //error_log($e->getMessage(), 3, '/var/tmp/php.log');
        echo '{"error":{"text":' . $e->getMessage() . '}}';
    }
});

/* API for particular seller ... All Orders
 * 
 * URL : http://knowledgeflow.in/shopin/webservices/v1/api/sellerOrders/sellerOrders(shop_id)
 * 
 */
$app->post('/sellerOrders/:shop_id', function($shop_id) use ($app) {

    try {
        if($shop_id){
            //ordered,processed,delivered,cancelled,refund
            $data['ordered'] = getSellerOrder($shop_id,'ordered');
            $data['processed'] = getSellerOrder($shop_id,'processed');
            $data['delivered'] = getSellerOrder($shop_id,'delivered');
            $data['cancelled'] = getSellerOrder($shop_id,'cancelled');
            $data['refund'] = getSellerOrder($shop_id,'refund');
            
            $json['status'] = 'success';
            $json['message'] = 'All Order Lists.';
            $json['data'] = $data;
            echo json_encode($json);
        } else {
            $obj = new stdClass();
            $json['status'] = 'success';
            $json['message'] = 'No record found.';
            $json['data'] = $obj;
            echo json_encode($json);
        }
    } catch (PDOException $e) {
        //error_log($e->getMessage(), 3, '/var/tmp/php.log');
        echo '{"error":{"text":' . $e->getMessage() . '}}';
    }
});
$app->run();