<?php
session_start();

// Redirect to login if the user is not logged in
if (!isset($_SESSION['username'])) {
    header("Location: ../login.html");
    exit();
}

$currentUserId = $_SESSION['user_id'];

// Load the XML files
$rentalsXmlFile = '../rentals.xml';
$moviesXmlFile = '../movie.xml';

if (!file_exists($rentalsXmlFile) || !file_exists($moviesXmlFile)) {
    die('Rental history or movies data not available.');
}

$rentalsXml = simplexml_load_file($rentalsXmlFile);
$moviesXml = simplexml_load_file($moviesXmlFile);

if ($rentalsXml === false || $moviesXml === false) {
    die('Failed to load rental history or movies data.');
}
?>

<!DOCTYPE html>
<html lang="en">

<body>



    <div class="rental-history">
        <h1 class="topic">Movies You Have Rented</h1>
        <table>
            <thead>
                <tr>
                    <th>Movie Title</th>
                    <th>Rent Date</th>
                    <th>Return Due Date</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $hasRentals = false;

                // Loop through the rentals for the current user
                foreach ($rentalsXml->rental as $rental) {
                    if ($rental->user_id == $currentUserId && $rental->status == 'rent') {
                        $hasRentals = true;

                        // Find the corresponding movie title from movie.xml using movie_id
                        $movieTitle = 'Unknown Movie'; // Default title if not found
                        foreach ($moviesXml->movie as $movie) {
                            if ((string)$movie['id'] === (string)$rental->movie_id) {
                                $movieTitle = $movie->title;
                                break;
                            }
                        }

                        // Format rent and return dates
                        $rentDate = date('Y-m-d', strtotime($rental->rent_date));
                        $returnDueDate = date('Y-m-d', strtotime($rental->return_date));

                        // Output the rental details
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($movieTitle) . "</td>";
                        echo "<td>" . htmlspecialchars($rentDate) . "</td>";
                        echo "<td>" . htmlspecialchars($returnDueDate) . "</td>";
                        echo "</tr>";
                    }
                }

                if (!$hasRentals) {
                    echo "<tr><td colspan='3'>No rental history found for your account.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

</body>

</html>