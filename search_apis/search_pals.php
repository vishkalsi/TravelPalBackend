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
        if (isset($decoded['key'])) {
            require '../dbconn.php';
            require '../utils/constants.php';
            $key = mysqli_real_escape_string($connection, $decoded['key']);
            $myUserId = mysqli_real_escape_string($connection, $decoded['myUserId']);
            $query = "select * from users where user_full_name like '%$key%' order by user_full_name asc";
            $results = mysqli_query($connection, $query) or die('Error : ' . mysqli_error($connection));
            $location = array();
            while ($row = mysqli_fetch_array($results)) {
                $userId = $row['primary_id'];

                $isFollowing = false;
                $checkRes = mysqli_query($connection, "select * from follow_unfollow where follower_id = $myUserId and user_id = $userId");
                $isFollowing = mysqli_num_rows($checkRes) > 0;
                $name = $row['user_full_name'];
                $dp = $baseURL.$row['user_display_picture'];
                $locationId = (int)$row['location_id'];
                array_push($location,  array(
                    "userId"=>$userId,
                    "userFullName"=>$name,
                    "userDP"=>$dp,
                    "isFollowingUser"=>$isFollowing
                ));
            }

            $response = array('response' => 'success', 'responseCode' => 200, "data"=>$location);
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
