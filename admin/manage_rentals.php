<?php
// Load the rentals XML file
$rentals = simplexml_load_file("../rentals.xml");
$rentalError = null; // Initialize rental error variable

if ($rentals === false) {
    $rentalError = "Failed to load rentals.";
    echo json_encode(['status' => 'error', 'message' => $rentalError]);
    exit;
}

// Load the movies XML file
$movies = simplexml_load_file("../movie.xml");
$movieError = null; // Initialize movie error variable

if ($movies === false) {
    $movieError = "Failed to load movies.";
}

// Load the users XML file
$users = simplexml_load_file("../users.xml");
$userError = null; // Initialize user error variable

if ($users === false) {
    $userError = "Failed to load users.";
}

// Function to toggle rental status via AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['rental_id'])) {
    foreach ($rentals->rental as $rental) {
        // Check if the rental ID matches the one sent from the form
        if ((string)$rental['id'] === (string)$_POST['rental_id']) {
            // Toggle the status between 'rent' and 'return'
            if ($rental->status == 'rent') {
                $rental->status = 'return';
            } else {
                $rental->status = 'rent';
            }
            $rentals->asXML('../rentals.xml'); // Save the updated rentals XML file

            // Return success message with JSON
            echo json_encode(['status' => 'success', 'message' => 'Rental status updated successfully']);
            exit;
        }
    }

    // If rental ID not found
    echo json_encode(['status' => 'error', 'message' => 'Rental ID not found.']);
    exit;
}

// Display messages after the rental status change
if (isset($_GET['status']) && $_GET['status'] === 'updated') {
    $message = "Rental status updated successfully.";
}
?>



<body>
    <h1 class="topic">Manage Rentals</h1>
    <?php if (isset($message)): ?>
        <div id="message" style="color:green;"><?php echo $message; ?></div>
    <?php endif; ?>
    <div id="message-error" style="display:none; color:red;"></div> <!-- Error Message Display Area -->

    <?php if ($rentalError): ?>
        <p><?php echo $rentalError; ?></p>
    <?php else: ?>
        <table border="1">
            <thead>
                <tr>
                    <th>User</th>
                    <th>Movie</th>
                    <th>Rent Date</th>
                    <th>Return Date</th>
                    <th>Status</th>
                    <th>Change Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($rentals->rental as $rental): ?>
                    <?php
                    // Find the corresponding user for the rental
                    $user = null;
                    foreach ($users->user as $u) {
                        if ($u['id'] == $rental['user_id']) {
                            $user = $u;
                            break;
                        }
                    }
                    // Check if user was found
                    $username = $user ? htmlspecialchars($user->username) : 'Unknown User';

                    // Find the corresponding movie for the rental
                    $movie = null;
                    foreach ($movies->movie as $m) {
                        if ($m['id'] == $rental['movie_id']) {
                            $movie = $m;
                            break;
                        }
                    }
                    // Check if movie was found
                    $title = $movie ? htmlspecialchars($movie->title) : 'Unknown Movie';
                    ?>
                    <tr id="rental-<?php echo $rental['id']; ?>">
                        <td>
                            <?php
                            $foundUser = false; // Flag to check if user is found
                            foreach ($users->user as $user) {
                                if ((string)$user['id'] === (string)$rental->user_id) {
                                    echo htmlspecialchars($user->username); // Display username
                                    $foundUser = true; // Set flag to true if user is found
                                    break; // Exit the loop once the match is found
                                }
                            }
                            if (!$foundUser) {
                                echo "Unknown User"; // Display if user is not found
                            }
                            ?>
                        </td>
                        <td>
                            <?php
                            $foundMovie = false; // Flag to check if movie is found
                            foreach ($movies->movie as $movie) {
                                if ((string)$movie['id'] === (string)$rental->movie_id) {
                                    echo htmlspecialchars($movie->title); // Display movie title
                                    $foundMovie = true; // Set flag to true if movie is found
                                    break; // Exit the loop once the match is found
                                }
                            }
                            if (!$foundMovie) {
                                echo "Unknown Movie"; // Display if movie is not found
                            }
                            ?>
                        </td>

                        <td><?php echo htmlspecialchars($rental->rent_date); ?></td>
                        <td><?php echo htmlspecialchars($rental->return_date); ?></td>
                        <td class="rental-status"><?php echo htmlspecialchars($rental->status); ?></td>
                        <td>
                            <input type="checkbox" class="status-toggle" data-rental-id="<?php echo $rental['id']; ?>" <?php echo $rental->status == 'return' ? 'checked' : ''; ?>>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>

        </table>
    <?php endif; ?>



    <script>
        $(document).ready(function() {
            $('.status-toggle').on('change', function() {
                var rentalId = $(this).data('rental-id');
                var isChecked = $(this).is(':checked');

                $.ajax({
                    url: 'manage_rentals.php',
                    method: 'POST',
                    data: {
                        rental_id: rentalId
                    },
                    success: function(response) {
                        var result = JSON.parse(response);
                        if (result.status === 'success') {
                            $('#rental-' + rentalId + ' .rental-status').text(isChecked ? 'return' : 'rent');
                            $('#message-error').hide(); // Hide error message if successful
                        } else {
                            $('#message-error').text(result.message).show();
                            $(this).prop('checked', !isChecked); // Revert the toggle state
                        }
                    }.bind(this),
                    error: function() {
                        alert('Failed to update status.');
                    }
                });
            });
        });
    </script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        $(document).ready(function() {
            $('.status-toggle').on('change', function() {
                var rentalId = $(this).data('rental-id');
                var isChecked = $(this).is(':checked');

                $.ajax({
                    url: 'manage_rentals.php', // Update with the actual path if needed
                    method: 'POST',
                    data: {
                        rental_id: rentalId
                    },
                    success: function(response) {
                        var result = JSON.parse(response);
                        if (result.status === 'success') {
                            // Update the rental status in the table
                            $('#rental-' + rentalId + ' .rental-status').text(isChecked ? 'return' : 'rent');
                            $('#message-error').hide(); // Hide error message if successful
                        } else {
                            $('#message-error').text(result.message).show();
                            // Revert the checkbox state if the request failed
                            $(this).prop('checked', !isChecked);
                        }
                    }.bind(this),
                    error: function() {
                        alert('Failed to update status.');
                        // Revert the checkbox state if there is an error
                        $(this).prop('checked', !isChecked);
                    }.bind(this)
                });
            });
        });
    </script>

</body>

</html>