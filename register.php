<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = htmlspecialchars($_POST['username']);
    $password = $_POST['password'];

    // Load the XML file
    $xml = simplexml_load_file('users.xml');

    // Check if the username already exists
    foreach ($xml->user as $user) {
        if ($user->username == $username) {
            echo "Username already exists!";
            exit;
        }
    }

    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Get the last user ID and increment for the new user
    $lastUser = $xml->user[count($xml->user) - 1];
    $newId = (int)$lastUser['id'] + 1;

    // Add the new user to the XML file
    $newUser = $xml->addChild('user');
    $newUser->addAttribute('id', $newId);
    $newUser->addChild('username', $username);
    $newUser->addChild('password', $hashedPassword);

    // Save the updated XML file
    $xml->asXML('users.xml');

    echo "Account created successfully!";
} else {
    echo "Invalid request!";
}
