<?php

function reverse_string($str){
    return strrev($str);
}

function encode_string($str){
  $data = base64_encode($str);
  $data = str_replace(array('+', '/', '='), array('-', '_', ''), $data);
  return $data;
  //return base64_encode($str);
}


function uploadBase64Image($userId,$profilePhoto,$uploadPath)
{
    $fileName = 'pic_'.time().$userId.'.jpg';
   // $imageData = base64_decode($profilePhoto);
    //$source = imagecreatefromstring($imageData);
    //imagejpeg($source,$uploadPath.$fileName,80);
    //imagedestroy($source);
    base64_to_jpeg($profilePhoto, $uploadPath.$fileName);
    
    
    return $fileName;
}

function base64_to_jpeg($base64_string, $output_file) {
    $ifp = fopen($output_file, "wb"); 

    $data = explode(',', $base64_string);

    fwrite($ifp, base64_decode($data[0])); 
    fclose($ifp); 

    return $output_file; 
}

function big_rand($len){
   $characters = 'abcdefghijklmnopqrstuvwxyz0123456789';
   $string = '';
   $max = strlen($characters) - 1;
   for ($i = 0; $i < $len; $i++) {
        $string .= $characters[mt_rand(0, $max)];
   }
   return $string;
}

function sendEmail($email, $subject, $body, $from = '', $cc = '', $bcc = '') {
          
      $mail = new PHPMailer;
     
      $mail->isSMTP();                                      // Set mailer to use SMTP
      $mail->Host = 'ssl://smtp.gmail.com';  // Specify main and backup SMTP servers
      $mail->SMTPAuth = true;                               // Enable SMTP authentication
      $mail->Username = 'lmsuser3@gmail.com';                 // SMTP username
      $mail->Password = 'lmsserver1234';                           // SMTP password
      $mail->Port = 465; 
      $mail->setFrom($from, 'LMS');
      $mail->addAddress($email);     // Add a recipient
      $mail->isHTML(true);      // Set email format to HTML

      $mail->Subject = $subject;
      $mail->Body    = $body;
     
      if(!$mail->Send())
        return 0;
      else
       return 1;
     
  
}





function sendAndroidPushNotification($apiKey,$registrationIds,$message) 
{
    //echo "hello android";
    $fields['registration_ids'] = $registrationIds;
    $fields['data']    = $message;


    $headers = array(
                'Authorization: key=' . $apiKey,
                'Content-Type: application/json'
                );

    $ch = curl_init();
    curl_setopt( $ch,CURLOPT_URL, 'https://android.googleapis.com/gcm/send' );
    curl_setopt( $ch,CURLOPT_POST, true );
    curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
    curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
    curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
    curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
    $result = curl_exec($ch );
    curl_close( $ch );

    return $result;
}

function sendIosPushNotification() 
{
    echo "hello ios";
}

function getDeviceTokenByUserId($id)
{
    $db = getDB();
    $query = "select ud_device_token from user_detail where ud_id=".$id;
    $stmt = $db->query($query);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);
    $db = null;
    return $data['ud_device_token'];
}

function getSellerOrder($seller_id,$status)
{
    $db = getDB();
    $query = "SELECT *
FROM `transactions`
WHERE `seller_id` =".$seller_id."
AND STATUS = '".$status."'";
    $stmt = $db->query($query);
            if ($stmt->rowCount() > 0) {
            $orderData = $stmt->fetchAll(PDO::FETCH_ASSOC);
            for ($i = 0; $i < count($orderData); $i++) {

                $data[$i]['order_id'] = $orderData[$i]['order_id'];
                $data[$i]['product_id'] = $orderData[$i]['product_id'];
                $data[$i]['product_name'] = $orderData[$i]['product_name'];
                $data[$i]['product_price'] = $orderData[$i]['product_price'];
                $data[$i]['payment_mode'] = $orderData[$i]['payment_mode'];
                $data[$i]['order_datetime'] = $orderData[$i]['order_datetime'];
                $data[$i]['delivered_datetime'] = $orderData[$i]['delivered_datetime'];
                $data[$i]['cancellation_datetime'] = $orderData[$i]['cancellation_datetime'];

            }

            $json['data'] = $data;
        }else{
           $json['data'] = Null; 
        } 
    $db = null;
    return $json;
}


function uploadImage($user_id,$image,$target_path){

//$path_parts = pathinfo($image);
//$t = $path_parts['dirname'].'-----'.$path_parts['basename'].'-------'.$path_parts['extension'].'------'.$path_parts['filename'];
$filename = $image['name'];
//$path_parts = pathinfo($filename);
$tmp=explode(".", $image['name']);
$extension = end($tmp);
$tempName = explode('.', $filename); 

$newFileName = $tempName[0].time().'.'.$extension;

$target_path = $target_path . $newFileName; 

if(move_uploaded_file($image["tmp_name"], $target_path)) {
    return $newFileName;
} else{
    return NULL;
}


}


function updatePoints($userid){
  $db = getDB();
   $query = $db->query("insert into user_earned_points(`user_id`,`earned_points`) "
                    . "values('". $userid . "',50)");
   $Id = $db->lastInsertId();
   $sql = "SELECT * FROM user_bucket where user_id='" . $userid . "'";
   $stmt = $db->query($sql);
   if($stmt->rowCount() > 0) {
     
     $db->query("update user_bucket set `total_points`= (`total_points`+ 50) where user_id=" . $userid);
   }else{
      $query = $db->query("insert into user_bucket(`user_id`,`total_points`) "
                    . "values('". $userid . "',`total_points`+50)");
   }
   return $Id;
}