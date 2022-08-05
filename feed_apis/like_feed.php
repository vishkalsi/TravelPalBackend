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
            $postId = $decoded['postId'];
            $res = mysqli_query($connection, "select * from post where post_id = $postId");
            $row = mysqli_fetch_array($res);
            $postByUserId = $row['user_id'];
            $isLiked = !empty($decoded['isLiked']);
            if($isLiked)
            {
                $query = "INSERT INTO `like_post` ( `post_id`, `like_by_user_id`, `date`) VALUES ('$postId', '$userId', current_timestamp())";
                mysqli_query($connection, "INSERT INTO `notifications` ( `notification_by_user_id`, `notification_for_user_id`, `notification_action_id`, `notification_action`, `notification_time`, notification_text_id) VALUES ( '$userId', '$postByUserId', '$postId', '$NOTIFICATION_ACTION_POST', current_timestamp(),'$NOTIFICATION_TYPE_LIKE')");
            }
            else {
                $query = "delete from like_post where post_id = $postId and like_by_user_id = $userId";
                mysqli_query($connection,"delete from notifications where notification_by_user_id = $userId and notification_for_user_id = $postByUserId and notification_action = $NOTIFICATION_ACTION_POST and  notification_text_id = $NOTIFICATION_TYPE_LIKE  ");
            }
            mysqli_query($connection, $query);


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
