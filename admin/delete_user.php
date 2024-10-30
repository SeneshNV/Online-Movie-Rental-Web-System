<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['id'])) {
        $userId = $_POST['id'];

        // Load the XML file
        $xml = simplexml_load_file('../users.xml');
        if ($xml === false) {
            echo "Failed to load users XML.";
            exit;
        }

        // Find the user by its ID and remove it
        $found = false;
        foreach ($xml->user as $user) {
            if ((string)$user['id'] === $userId) {
                $dom = dom_import_simplexml($user);
                $dom->parentNode->removeChild($dom);
                $found = true;
                break;
            }
        }

        if ($found) {
            // Save the updated XML back to the file
            $xml->asXML('../users.xml');
            echo "User deleted successfully.";
        } else {
            echo "User not found.";
        }
    } else {
        echo "User ID not provided.";
    }
} else {
    echo "Invalid request method.";
}
