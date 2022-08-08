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
            $myUserId = mysqli_real_escape_string($connection, $decoded['myUserId']);
            $userId = $decoded['userId'];
            $isLiked = !empty($decoded['isFollowingUser']);
            if($isLiked)
            {
                mysqli_query($connection, "INSERT INTO follow_unfollow (user_id, follower_id) values ('$userId', '$myUserId')");
            }
            else {
                $query = "delete from follow_unfollow where user_id = $userId and follower_id = $myUserId";
                mysqli_query($connection,$query);
            }



            $response = array('response' => 'success', 'responseCode' => 200
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
