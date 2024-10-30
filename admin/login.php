<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start(); // Start session for storing login status

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = htmlspecialchars($_POST['username']);
    $password = $_POST['password'];

    // Load the XML file
    $xml = simplexml_load_file('admin.xml');

    // Search for the user in the XML file
    foreach ($xml->user as $user) {
        if (trim($user->username) == $username) {
            // Verify the password using password_verify
            if (password_verify($password, trim($user->password))) {
                $_SESSION['admin_username'] = (string)$user->username;
                // Send a success response as JSON
                echo json_encode(["success" => true, "message" => "Login successful!"]);
                exit;
            } else {
                // Send an error response as JSON
                echo json_encode(["success" => false, "message" => "Invalid password!"]);
                exit;
            }
        }
    }

    // Send a user not found response as JSON
    echo json_encode(["success" => false, "message" => "User not found!"]);
} else {
    // Send an invalid request response as JSON
    echo json_encode(["success" => false, "message" => "Invalid request!"]);
}
?>
