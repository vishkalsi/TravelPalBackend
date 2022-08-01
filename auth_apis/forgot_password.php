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
        if (isset($decoded['email']) && isset($decoded['password']) && isset($decoded['deviceId'])) {
            require '../dbconn.php';
            $userEmail = mysqli_real_escape_string($connection, $decoded['email']);
            $query = "SELECT * FROM users WHERE user_email='$userEmail'";
            $results = mysqli_query($connection, $query) or die('Error : ' . mysqli_error($connection));
            if ($row = mysqli_fetch_array($results)) {
                // $query = "update users set otp = '$otp' WHERE user_email='$userEmail'";
                // $results = mysqli_query($connection, $query) or die('Error : ' . mysqli_error($connection));
                $response = array('response' => 'success', 'responseCode' => 200, 'data' => array('email' => $userEmail));
                $data = array('response' => 'success', 'responseCode' => 200, 'otp' => $otp, 'userName' => ucwords($row['user_full_name']), 'email' => $userEmail);
            } else {
                $response = array('response' => 'error', 'responseCode' => 403, 'errorMessage' => 'Email is not registered with any account');
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
