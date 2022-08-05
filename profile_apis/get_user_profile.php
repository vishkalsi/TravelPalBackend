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
            require '../utils/constants.php';
            $userId = mysqli_real_escape_string($connection, $decoded['myUserId']);
            $otherUserId = mysqli_real_escape_string($connection, $decoded['userId']);
            $isMyProfile = $userId == $otherUserId;
            $query = "SELECT * FROM users WHERE primary_id='$otherUserId'";
            $results = mysqli_query($connection, $query) or die('Error : ' . mysqli_error($connection));
            if ($row = mysqli_fetch_array($results)) {
                $userEmail = $row['user_email'];
                $userAddress = $row['user_address'];
                $userGender = $row['user_gender'] == '0' ? "Male":"Female";
                $userDP = $row['user_display_picture'];
                $userPhone = $row['user_phone_number'];
                $userDOB = $row['user_dob'];
                $userAbout = $row['user_about'];
                $userRating = 0;
                $userFullName = $row['user_full_name'];
                $response = array('response' => 'success', 'responseCode' => 200, 'data' => 
                array(
                    'userEmail' => $userEmail,
                    'userAddress' => $userAddress,
                    'userFullName' => $userFullName,
                    'userGender' => $userGender,
                    'userDP' => $baseURL.$userDP,
                    'userDOB' => $userDOB,
                    'userAbout' => $userAbout,
                    'followerCount' => 0,
                    'followingCount' => 0,
                    'userPhone' => $userEmail,
                    'userRating' => $userRating,
                    'isFollowing' => false,
                    'isMyProfile' => $isMyProfile
            ));
            } else {
                $response = array('response' => 'error', 'responseCode' => 403, 'errorMessage' => 'Something went wrong');
            }
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
