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
        if (isset($decoded['type'])) {
            require '../dbconn.php';
            require_once "../utils/constants.php";
            $userId = mysqli_real_escape_string($connection, $decoded['userId']);
            $myUserId = mysqli_real_escape_string($connection, $decoded['myUserId']);
            if($decoded['type'] == '0') // followers
            {
                $query = "select follower_id as id from follow_unfollow where user_id = $userId";
            }
            else {
                $query = "select user_id as id from follow_unfollow where follower_id = $userId  order by comment_id desc";
            }

            $result = mysqli_query($connection, $query);
            $list = array();
            while ($row = mysqli_fetch_array($result))
            {
                $id = $row['id'];
                $isFollowing = false;
                $checkRes = mysqli_query($connection, "select * from follow_unfollow where follower_id = $myUserId and user_id = $id");
               $isFollowing = mysqli_num_rows($checkRes) > 0;
                $nameResult = mysqli_query($connection, "select * from users where primary_id = $id");
                $rowName = mysqli_fetch_array($nameResult);
                $name = $rowName['user_full_name'];
                $dp = $baseURL.$rowName['user_display_picture'];
                $time = $row['date'];
                array_push($list, array(
                    "userId"=>(int)$id,
                    "fullName"=>$name,
                    "isFollowingUser"=>$isFollowing,
                    "userProfilePic"=>$dp,
                    "createdAt"=>$time
                ));
            }
            $response = array('response' => 'success', 'responseCode' => 200, "data"=>$list
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
