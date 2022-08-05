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
            $query = "select * from notifications where notification_for_user_id = $userId  order by notification_id desc";
            $result = mysqli_query($connection, $query);
            $notifications = array();
            while ($row = mysqli_fetch_array($result))
            {
                $notificationBy = $row['notification_by_user_id'];
                $nameResult = mysqli_query($connection, "select * from users where primary_id = $notificationBy");
                $rowName = mysqli_fetch_array($nameResult);
                $notificationByName = $rowName['user_full_name'];
                $notificationAction = $row['notification_action'];
                $notificationActionId = $row['notification_action_id'];
                $notificationDate = $row['notification_time'];
                $notificationTextId = $row['notification_text_id'];
                $notificationText = $notificationByName . " ";
                if($notificationTextId == $NOTIFICATION_TYPE_LIKE) {
                    $notificationText.="liked your post.";
                }
                elseif($notificationTextId == $NOTIFICATION_TYPE_FOLLOW) {
                    $notificationText.="started following you.";
                }
                elseif($notificationTextId == $NOTIFICATION_TYPE_COMMENT) {
                    $notificationText.="commented on your post.";
                }
                array_push($notifications, array(
                   "notificationText"=>$notificationText,
                   "notificationAction"=>(int)$notificationAction,
                   "notificationActionId"=>(int)$notificationActionId,
                   "notificationDate"=>$notificationDate
                ));
            }


            $response = array('response' => 'success', 'responseCode' => 200, "object"=>$notifications
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
