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

        if (isset($decoded['userId']) && isset($decoded['otp'])) {
            require '../dbconn.php';
            $userId = mysqli_real_escape_string($connection, $decoded['userId']);
            $otp = mysqli_real_escape_string($connection, $decoded['otp']);
            $query = "select * from users where primary_id = $userId and otp = $otp";
	    $results = mysqli_query($connection, $query) or die('Error : ' . mysqli_error($connection));
            if ($row = mysqli_fetch_array($results)) {
                mysqli_query($connection, "update users set is_verified = 1 where primary_id = $userId");
                $response = array('response' => 'success', 'responseCode' => 200);
                echo json_encode($response);
            } else {
                header("HTTP/1.1 403 Invalid OTP");
                $response = array('response' => 'error', 'responseCode' => 403, 'errorMessage' => 'Invalid OTP, please try again');
                echo json_encode($response);
            }
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
