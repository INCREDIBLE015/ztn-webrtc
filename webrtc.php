<?php 


// $connect = mysqli_connect('localhost','swifbhgv_root','Brainbox100','swifbhgv_webrtc');

$connect = mysqli_connect('remotemysql.com','dgzp14KWPv','LHG9X4aIdX','dgzp14KWPv');


if(isset($_POST['login'])){
    
    extract($_POST);
    
    $getUser=mysqli_query($connect,"SELECT * FROM users WHERE email = '$email' AND password = '$password' LIMIT 1");
        
    if(mysqli_num_rows($getUser)>0){
        $user=mysqli_fetch_array($getUser);
        
        //send otp
        
        $otp=rand(1000,9999);
        
        $sms_array = array (
            'sender'    => 'WebRTC',
            'number' => $user['phone'],        //This can be set as desired. 3 = Deliver message to DND phone numbers via the corporate route
            'code' => $otp
        );
        
        
        $params = http_build_query($sms_array);
        $ch = curl_init(); 
        curl_setopt($ch, CURLOPT_URL,'https://neutrinoapi.net/verify'); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,true); 
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POST, 1); 
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        $response = curl_exec($ch); 
        curl_close($ch); 
        
        mail($user['email'],'OTP','Your verification code is: '.$otp);
        
        $response=[
            'status'=>true,
            'message'=>'login successfull',
            'data'=>[
                'name'=>$user['name'],
                'phone'=>$user['phone'],
                'otp'=>"$otp",
            ]
        ];
        
    }
    
    else{
        $response=[
            'status'=>false,
            'message'=>'login unsuccessfull',
            'data'=>[
                'name'=>null,
                'phone'=>null,
                'otp'=>null
            ]
        ];
    }
    
    echo json_encode($response);
    die();
}

if(isset($_POST['register'])){
    
    extract($_POST);
    
    if(mysqli_query($connect,"INSERT INTO users VALUES(null,'$name','$email','$phone','$password')")){
        
        
        //send otp
        
        $otp=rand(1000,9999);
        
        $sms_array = array (
            'sender'    => 'WebRTC',
            'number' => $user['phone'],        //This can be set as desired. 3 = Deliver message to DND phone numbers via the corporate route
            'code' => $otp
        );
        
        
        $params = http_build_query($sms_array);
        $ch = curl_init(); 
        curl_setopt($ch, CURLOPT_URL,'https://neutrinoapi.net/verify'); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,true); 
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POST, 1); 
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        $response = curl_exec($ch); 
        curl_close($ch); 
        
        mail($email,'OTP','Your verification code is: '.$otp);

        
        $response=[
            'status'=>true,
            'message'=>'registration successfull',
            'data'=>[
                'name'=>$name ?? null,
                'phone'=>$phone ?? null,
                'otp'=>"$otp",
            ]
        ];
        
    }
    
    else{
        $response=[
            'status'=>true,
            'message'=>'registration unsuccessfull',
            'data'=>[
                'name'=>null,
                'phone'=> null,
                'otp'=>null,
            ]
        ];
    }
    
    echo json_encode($response);
    die();
}

?>