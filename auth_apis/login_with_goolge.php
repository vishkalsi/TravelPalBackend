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
        if (isset($decoded['email']) && isset($decoded['googleId']) && isset($decoded['deviceId'])) {
            require '../dbconn.php';
            $emailOrPhone = mysqli_real_escape_string($connection, $decoded['email']);
            $googleId = mysqli_real_escape_string($connection, $decoded['googleId']);
            $deviceId = mysqli_real_escape_string($connection, $decoded['deviceId']);
            $encryptedPassword = md5($password);
            $query = "Select primary_id from users WHERE user_email='$userEmail' OR user_google_id='$googleId' ";
            if($userEmail == '' || $userEmail == null) {
                $query = "Select primary_id from users WHERE user_google_id='$googleId' ";
            }
    	    $results = mysqli_query($connection, $query) or die('Error : ' . mysqli_error($connection));
            if ($row = mysqli_fetch_array($results)) {
                $userId = $row['primary_id'];
		        $data = array("userId"=>$userId);
                $response = array('response' => 'success', 'responseCode' => 200, 'data' => $data);
                echo json_encode($response);
            } else {
                $response = array('response' => 'error', 'responseCode' => 404, 'errorMessage' => 'No account found. Please sign up to continue.');
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
