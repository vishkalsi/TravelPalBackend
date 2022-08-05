<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    //Receive the RAW post data.
    $content = trim($_REQUEST['imageData']);

//Attempt to decode the incoming RAW post data from JSON.
    $decoded = json_decode($content, true);
    if (!is_array($decoded)) {
        $response = array('response' => 'error', 'responseCode' => 400, 'errorMessage' => 'Bad Request');
        echo json_encode($response);
    } else {
        if (isset($decoded['type']) ) {

            require '../dbconn.php';
            require_once "../utils/constants.php";
            $myUserId = $decoded['myUserId'];
            $target_dir = "../uploads/";
            $res_dir = "uploads/";
            $rand = rand(111111111,999999999);
    if($decoded['type'] == "0") // dp
    {
        $subDir = "profile_pictures/";
    }
    else // feed
        {
        $subDir = "images/";
        }

$target_file = $target_dir. $subDir ."IMG-".$rand. ".jpg";
$uploadOk = 1;
            $response_file = $res_dir. $subDir ."IMG-".$rand. ".jpg";
            mysqli_query($connection, "update users set user_display_picture = '$response_file' where primary_id = $myUserId");
    if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
                   $response = array('response' => 'success', 'responseCode' => 200,"data"=>$response_file);
            echo json_encode($response);
    } else {
        echo "Sorry, there was an error uploading your file.";
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

?>