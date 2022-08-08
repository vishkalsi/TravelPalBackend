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
        if (isset($decoded['caption']) && isset($decoded['postType']) && isset($decoded['location'])) {
            require '../dbconn.php';
            $caption = mysqli_real_escape_string($connection, $decoded['caption']);
            $postType = mysqli_real_escape_string($connection, $decoded['postType']);
            $location = mysqli_real_escape_string($connection, $decoded['location']);
            $imageURL = mysqli_real_escape_string($connection, $decoded['imageURL']);
            $locationLat = 0;
            $locationLng = 0;
            $myUserId = $decoded['myUserId'];
            $travelDate = "";
            $travelGender = "";
            if($postType == "1") // looking for
            {
                $travelDate = $decoded['travelDate'];
                $travelGender = $decoded['travelGender'];
            }
            mysqli_query($connection, "INSERT INTO `post` (`caption`, `user_id`, `date`, `location`, `latitude`, `longitude`, `post_type`, `image_url`, `travel_date`, `travel_with_gender`) VALUES ('$caption', '$myUserId', current_timestamp(), '$location', '$locationLat', '$locationLng', '$postType', '$imageURL', '', '')");
            $response = array('response' => 'success', 'responseCode' => 200);
            echo json_encode($response);
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
