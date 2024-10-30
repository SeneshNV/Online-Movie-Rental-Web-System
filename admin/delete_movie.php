<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['id'])) {
        $movieId = $_POST['id'];

        // Load the XML file
        $xml = simplexml_load_file('../movie.xml');
        if ($xml === false) {
            echo "Failed to load movie XML.";
            exit;
        }

        // Find the movie by its ID and remove it
        $found = false;
        foreach ($xml->movie as $movie) {
            if ((string)$movie['id'] === $movieId) {
                $dom = dom_import_simplexml($movie);
                $dom->parentNode->removeChild($dom);
                $found = true;
                break;
            }
        }

        if ($found) {
            // Save the updated XML back to the file
            $xml->asXML('../movie.xml');
            echo "Movie deleted successfully.";
        } else {
            echo "Movie not found.";
        }
    } else {
        echo "Movie ID not provided.";
    }
} else {
    echo "Invalid request method.";
}
