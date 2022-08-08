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
        if (isset($decoded['postId'])) {
            require '../dbconn.php';
            require_once "../utils/constants.php";
            $postId = mysqli_real_escape_string($connection, $decoded['postId']);
            $query = "select * from comments where post_id = $postId  order by comment_id desc";
            $result = mysqli_query($connection, $query);
            $comments = array();
            while ($row = mysqli_fetch_array($result))
            {
                $userId = $row['comment_by_user_id'];
                $nameResult = mysqli_query($connection, "select * from users where primary_id = $userId");
                $rowName = mysqli_fetch_array($nameResult);
                $name = $rowName['user_full_name'];
                $dp = $baseURL.$rowName['user_display_picture'];
                $comment = $row['comment'];
                $time = $row['date'];
                array_push($comments, array(
                    "userId"=>(int)$userId,
                    "userFullName"=>$name,
                    "userDP"=>$dp,
                    "comment"=>$comment,
                    "createdAt"=>$time
                ));
            }


            $response = array('response' => 'success', 'responseCode' => 200, "data"=>$comments
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
