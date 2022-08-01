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
        if (isset($decoded['userId'])) {
            require '../dbconn.php';
            $userAddress =mysqli_real_escape_string($connection, $row['userAddress']);
            $userGender = mysqli_real_escape_string($connection,$row['userGender']) ;
            $userDOB = mysqli_real_escape_string($connection,$row['userDOB']);
            $userAbout =mysqli_real_escape_string($connection, $row['userAbout']);
            $userId = mysqli_real_escape_string($connection, $decoded['userId']);
            $userFullName = mysqli_real_escape_string($connection, $decoded['userFullName']);
            $query = "update users set users set user_address = '$userAddress',user_gender = '$userGender',user_dob = '$userDOB',user_about = '$userAbout',user_full_name = '$userFullName' where user_id = $userId";
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
