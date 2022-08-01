<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    $content = trim(file_get_contents("php://input"));
    $decoded = json_decode($content, true);
    if (!is_array($decoded)) {
        header("HTTP/1.1 400 Bad Request");
        $response = array('response' => 'error', 'responseCode' => 400, 'errorMessage' => 'Bad Request');
        echo json_encode($response);
    } else  {
        if (isset($decoded['userId'])) {
            require '../dbconn.php';
            $userId = $decoded['userId'];
            $myUserId = $decoded['myUserId'];
            $rating = (int) $decoded['rating'];
            $review = mysqli_real_escape_string($connection,$decoded['review']);
            $query = "insert into user_ratings (rating_by_user_id, user_id, review, rating) values ('$userId','$myUserId','$review','$rating')";
            mysqli_query($connection, $query) or die('Error : ' . mysqli_error($connection));
            $response = array('response' => 'success', 'responseCode' => 200);
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
