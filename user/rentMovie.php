<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'You must be logged in to manage your account!']);
    exit;
}

// Ensure timezone is set correctly
date_default_timezone_set('Asia/Colombo'); // Adjust to your timezone

if (!isset($_SESSION['username'])) {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in.']);
    exit();
}

// Get the movie ID from the POST request
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['movieId'])) {
    $movieId = htmlspecialchars($_POST['movieId']);
    $username = $_SESSION['username'];

    // Load the users.xml file to find the user's ID based on their username
    $usersFile = '../users.xml';
    if (file_exists($usersFile)) {
        $users = simplexml_load_file($usersFile);
        $userId = null;

        // Find the user by username and get their ID
        foreach ($users->user as $user) {
            if ($user->username == $username) {
                $userId = (string)$user['id'];  // Get the user ID
                break;
            }
        }

        // If user ID was found, proceed to store rental info
        if ($userId !== null) {
            // Load or create rentals.xml file
            $rentalsFile = '../rentals.xml';
            if (file_exists($rentalsFile)) {
                $rentals = simplexml_load_file($rentalsFile);
            } else {
                $rentals = new SimpleXMLElement('<rentals></rentals>');
            }

            // Prevent multiple insertions by checking for duplicate rentals
            $duplicateRental = false;
            foreach ($rentals->rental as $rental) {
                if ($rental->user_id == $userId && $rental->movie_id == $movieId && $rental->status == 'rent') {
                    $duplicateRental = true;
                    break;
                }
            }

            if (!$duplicateRental) {
                // Calculate the rent and return dates
                $rentDate = date('Y-m-d'); // Get current date
                $returnDate = date('Y-m-d', strtotime('+5 days')); // Calculate return date (5 days after rent date)

                // Find the highest rental ID and auto-increment it
                $lastRentalId = 0;
                foreach ($rentals->rental as $existingRental) {
                    if ((int)$existingRental['id'] > $lastRentalId) {
                        $lastRentalId = (int)$existingRental['id'];
                    }
                }
                $newRentalId = $lastRentalId + 1;  // Increment the last rental ID by 1

                // Add new rental entry
                $rental = $rentals->addChild('rental');
                $rental->addAttribute('id', $newRentalId);  // Set the new rental ID
                $rental->addChild('user_id', $userId);  // Store user ID
                $rental->addChild('movie_id', $movieId);
                $rental->addChild('rent_date', $rentDate);  // Add rent date
                $rental->addChild('return_date', $returnDate);  // Add return date
                $rental->addChild('status', 'rent');

                // Save the updated rentals.xml file
                $rentals->asXML($rentalsFile);

                // Update movie availability in movies.xml
                $movieFile = '../movie.xml';
                $movieTitle = '';  // Variable to hold the movie title
                if (file_exists($movieFile)) {
                    $movies = simplexml_load_file($movieFile);
                    foreach ($movies->movie as $movie) {
                        if ($movie['id'] == $movieId) {
                            // Optionally, add an attribute to indicate it has been rented
                            $movieTitle = (string)$movie->title;  // Get the movie title
                            break;
                        }
                    }
                    $movies->asXML($movieFile);
                }

                // Return success message with movie title
                echo json_encode(['status' => 'success', 'message' => "Movie '$movieTitle' rented successfully!"]);
            } else {
                // Return error message if the rental already exists
                echo json_encode(['status' => 'error', 'message' => 'You have already rented this movie.']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'User not found.']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Users file not found.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Movie ID not provided.']);
}
