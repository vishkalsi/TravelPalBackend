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
        if (isset($decoded['key'])) {
            require '../dbconn.php';
            require '../utils/constants.php';
            $key = mysqli_real_escape_string($connection, $decoded['key']);
            $query = "select * from locations where cities like '%$key%' order by cities asc";
            $results = mysqli_query($connection, $query) or die('Error : ' . mysqli_error($connection));
            $location = array();
            while ($row = mysqli_fetch_array($results)) {
                $country = $row['country'];
                $city = $row['cities'];
                $locationId = (int)$row['location_id'];
              array_push($location,  array(
                  "country"=>$country,
                  "city"=>$city,
                  "locationId"=>$locationId
              ));
            }
            $response = array('response' => 'success', 'responseCode' => 200, "data"=>$location);
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
