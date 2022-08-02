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
        if (isset($decoded['userEmail']) && isset($decoded['userPassword']) && isset($decoded['deviceId'])) {
            require '../dbconn.php';
            $emailOrPhone = mysqli_real_escape_string($connection, $decoded['userEmail']);
            $password = mysqli_real_escape_string($connection, $decoded['userPassword']);
            $deviceId = mysqli_real_escape_string($connection, $decoded['deviceId']);
            $encryptedPassword = md5($password);
            $query = "select primary_id from users WHERE (user_phone_number='$emailOrPhone' or user_email='$emailOrPhone') and user_password='$encryptedPassword'";
            $results = mysqli_query($connection, $query) or die('Error : ' . mysqli_error($connection));
            if ($row = mysqli_fetch_array($results)) {
                $userId = $row['primary_id'];
                $data = array("userId"=>$userId);
                $response = array('response' => 'success', 'responseCode' => 200, 'data' => $data);
                echo json_encode($response);
            } else {
                $query = "SELECT * FROM users WHERE (user_phone_number='$emailOrPhone' or user_email='$emailOrPhone')";
                $results = mysqli_query($connection, $query) or die('Error : ' . mysqli_error($connection));
                if ($row = mysqli_fetch_array($results)) {
                    header("HTTP/1.1 402 Password not match");

                    $response = array('response' => 'error', 'responseCode' => 455, 'errorMessage' => 'Password not match');
                } else {
                    header("HTTP/1.1 402 Invalid user id");

                    $response = array('response' => 'error', 'responseCode' => 452, 'errorMessage' => 'Invalid user id');
                }
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
