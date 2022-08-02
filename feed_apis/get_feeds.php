<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    $content = trim(file_get_contents("php://input"));
    $decoded = json_decode($content, true);
    if (!is_array($decoded)) {
        header("HTTP/1.1 400 Bad Request");
        $response = array('response' => 'error', 'responseCode' => 400, 'errorMessage' => 'Bad Request');
        echo json_encode($response);
    } else {
        if (isset($decoded['myUserId'])) {
            require '../dbconn.php';
            require_once "../utils/constants.php";
            $userId = mysqli_real_escape_string($connection, $decoded['myUserId']);
            $feeds = array();
            $query = "SELECT * FROM `post`, users where users.primary_id = post.user_id and post.post_type = 0 ORDER by post_id desc";
            $results = mysqli_query($connection, $query) or die('Error : ' . mysqli_error($connection));
            while ($row = mysqli_fetch_array($results)) {
                $userDP = $row['user_display_picture'];
                $userFullName = $row['user_full_name'];
                $postId = $row['post_id'];
                $pID = $row['primary_id'];
                $postType = $row['post_type'];
                $caption = $row['caption'];
                $travelOnDate = $row['travel_date'];
                $travelGender = $row['travel_gender'] == "" ? "": $row['travel_gender'] == "0" ? "Male":"Female" ;
                $location = mysqli_real_escape_string($connection, $row['location']);
                $imageURL = $baseURL.$row['image_url'];
                array_push($feeds,  array(
                    'postId'=>(int)$postId,
                    'userFullName' => $userFullName,
                    'userDP' => $userDP,
                    'postByUserId' => $pID,
                    'caption'=>$caption,
                    "location"=>$location,
                    "imageURL" => $imageURL,
                    "postType" => $postType,
                    "travelOnDate" => $travelOnDate,
                    "travelGender" => $travelGender,
                    "likes"=>1,
                    "comments"=>0,
                    "isLiked"=>true,
                    "isBookmarked"=>false,
                    "isFollowingUser"=>false
                ));

            }
            $response = array('response' => 'success', 'responseCode' => 200, 'data' =>$feeds
                   );
            echo json_encode($response);
            mysqli_close($connection);
        } else {
            $response = array('response' => 'error', 'responseCode' => 406, 'errorMessage' => 'Wrong arguments');
            echo json_encode($response);
        }
    }
} else {
    header("HTTP/1.1 401 Unauthorized");
    $response = array('response' => 'error', 'responseCode' => 401, 'errorMessage' => 'Unauthorized User');
    echo json_encode($response);
}
