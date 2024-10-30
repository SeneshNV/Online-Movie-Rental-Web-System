<?php
session_start();

if (!isset($_SESSION['admin_username'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get data from the form submission
    $id = isset($_POST['movieId']) ? $_POST['movieId'] : null; // Handle movieId
    $title = $_POST['title'];
    $director = $_POST['director'];
    $genre = $_POST['genre'];
    $year = $_POST['year'];
    $rating = $_POST['rating'];
    $available = $_POST['available'];
    $imageLink = $_POST['imageLink'];  // New field for image link

    // Load the existing movies XML file
    $movies = simplexml_load_file("../movie.xml");

    if ($movies === false) {
        echo "Error loading movie XML file.";
        exit;
    }

    if ($id) {
        // Edit the existing movie
        foreach ($movies->movie as $movie) {
            if ((string) $movie['id'] === $id) {
                $movie->title = $title;
                $movie->director = $director;
                $movie->genre = $genre;
                $movie->release_year = $year;
                $movie->rating = $rating;
                $movie->available = $available;

                // Only update image link if it is not empty
                if (!empty($imageLink)) {
                    $movie->image = $imageLink;  // Update image link only if it is not empty
                }
                break;
            }
        }
        $message = "Movie updated successfully!";
    } else {
        // Find the highest current movie ID
        $maxId = 0;
        foreach ($movies->movie as $movie) {
            $currentId = (int) $movie['id'];  // Cast ID to integer
            if ($currentId > $maxId) {
                $maxId = $currentId;
            }
        }

        // Increment ID for new movie
        $newId = $maxId + 1;

        // Create a new movie element
        $newMovie = $movies->addChild('movie');
        $newMovie->addAttribute('id', $newId); // Auto-incremented ID
        $newMovie->addChild('title', $title);
        $newMovie->addChild('director', $director);
        $newMovie->addChild('genre', $genre);
        $newMovie->addChild('release_year', $year);
        $newMovie->addChild('rating', $rating);
        $newMovie->addChild('available', $available);
        $newMovie->addChild('image', $imageLink);  // Save image link

        $message = "Movie added successfully!";
    }

    // Save the updated XML file
    $result = $movies->asXML("../movie.xml");

    if ($result) {
        echo $message;
    } else {
        echo "Failed to save movie.";
    }
}
