<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    $content = trim(file_get_contents("php://input"));
    error_log($content);
    $decoded = json_decode($content, true);
    if (!is_array($decoded)) {
        $response = array('response' => 'error', 'responseCode' => 400, 'errorMessage' => 'Bad Request');
        echo json_encode($response);
    } else {
        if (isset($decoded['userFullName']) && isset($decoded['userEmail']) && isset($decoded['userPassword'])) {
            require '../dbconn.php';
            $userEmail = mysqli_real_escape_string($connection, $decoded['userEmail']);
            $phoneNumber = mysqli_real_escape_string($connection, $decoded['userPhoneNumber']);
            $userDOB = mysqli_real_escape_string($connection, $decoded['userDOB']);
            $userName = mysqli_real_escape_string($connection, $decoded['userFullName']);
            $deviceId = mysqli_real_escape_string($connection, $decoded['deviceId']);
            $nId = mysqli_real_escape_string($connection, $decoded['notificationId']);
            $password = mysqli_real_escape_string($connection, $decoded['userPassword']);
            $encryptedPassword = md5($password);
            $gender = mysqli_real_escape_string($connection, $decoded['userGender']);
            $result = mysqli_query($connection, "select * from users where user_phone_number='$phoneNumber' or user_email='$userEmail'");
            if ($row = mysqli_fetch_array($result)) {
                header("HTTP/1.1 402 User already exist");
                $response = array('response' => 'error', 'responseCode' => 402, 'errorMessage' => 'User already exist!');
                echo json_encode($response);
            }
            else{
                $query = "insert into users (user_full_name,user_email,user_phone_number,user_password,user_display_picture,user_gender, notification_id, device_id, user_dob) values ('$userName','$userEmail','$phoneNumber','$encryptedPassword','','$gender','$nId','$deviceId','$userDOB')";
                if(mysqli_query($connection, $query))
                {
                    $otp = rand(1000,9999);
                    $last_id = mysqli_insert_id($connection);
                    mysqli_query($connection,"update users set otp = $otp where primary_id = $last_id");
                    mysqli_close($connection);
                    $isNewUser = true;
                    $to = $userEmail;
                    $subject = "TRAVEL PAL OTP";
                    $txt = "Hey There! Your TravelPal Verification code is ".$otp. ". Please enter this OTP to authorize your email address.";
                    $headers = "From: travelpal@godwillexecutors.com";
                    mail($to,$subject,$txt,$headers);
                    $object = array("userId" => $last_id);
                    $response = array('response' => 'success', 'responseCode' => 200, 'data' => $object, "message"=>"OTP SENT");
                    echo json_encode($response);
                }
            }
        }
        else {
            $response = array('response' => 'error', 'responseCode' => 406, 'errorMessage' => 'Wrong arguments');
            echo json_encode($response);
        }
    }
} else {
    header("HTTP/1.1 401 Unauthorized");
    $response = array('response' => 'error', 'responseCode' => 401, 'errorMessage' => 'Unauthorized User');
    echo json_encode($response);
}
