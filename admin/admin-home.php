<?php
session_start();

if (!isset($_SESSION['admin_username'])) {
    header("Location: login.php");
    exit;
}

// Load the movies XML file
$movies = simplexml_load_file("../movie.xml");
if ($movies === false) {
    $movieError = "Failed to load movies.";
} else {
    $movieError = null;
}

// Load the users XML file
$users = simplexml_load_file("../users.xml");
if ($users === false) {
    $userError = "Failed to load users.";
} else {
    $userError = null;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="styles_admin.css" />
    <title>Admin Home</title>
</head>

<body>
    <header>
        <h1>Admin Home</h1>

        <nav>
            <ul>
                <li><a href="#" id="addMovieBtn">Add Movies</a></li>
                <li><a href="#manageMoviesBtn2" id="manageMoviesBtn">Manage Movies</a></li>
                <li><a href="#" id="manageUsersBtn">Manage Users</a></li>
                <li><a href="#" id="manageRentalsBtn">Manage Rentals</a></li>
                <li><a href="../logout.php" class="logout">Logout</a></li>
            </ul>

        </nav>
    </header>

    <div id="content">
        <div id="moviesSection">
            <h1 class="topic">Add / Update Movies</h1>
            <form id="addMovieForm">
                <input type="hidden" id="movieId" />
                <div>
                    <label for="movieTitle">Title:</label>
                    <input type="text" id="movieTitle" placeholder="Movie Title" required />
                </div>
                <div>
                    <label for="movieDirector">Director:</label>
                    <input type="text" id="movieDirector" placeholder="Director" required />
                </div>
                <div>
                    <label for="movieGenre">Genre:</label>
                    <select id="movieGenre" required>
                        <option value="">Select Genre</option>
                        <option value="Action">Action</option>
                        <option value="Adventure">Adventure</option>
                        <option value="Animation">Animation</option>
                        <option value="Comedy">Comedy</option>
                        <option value="Drama">Drama</option>
                        <option value="Fantasy">Fantasy</option>
                        <option value="Horror">Horror</option>
                        <option value="Mystery">Mystery</option>
                        <option value="Romance">Romance</option>
                        <option value="Sci-fi">Sci-fi</option>
                        <option value="Thriller">Thriller</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                <div>
                    <label for="movieYear">Release Year:</label>
                    <input type="number" id="movieYear" placeholder="Release Year" min="1895" max="2024" required />
                </div>
                <div>
                    <label for="movieRating">Rating:</label>
                    <input type="number" step="0.1" id="movieRating" placeholder="Rating" min="0" max="10" required />
                </div>
                <div>
                    <label for="movieAvailable">Available:</label>
                    <select id="movieAvailable">
                        <option value="true">Available</option>
                        <option value="false">Not Available</option>
                    </select>
                </div>
                <div>
                    <label for="movieImageLink">Image URL:</label>
                    <input type="url" id="movieImageLink" name="imageLink" placeholder="Image Link" />
                </div>

                <button type="submit">Add / Update Movie</button>
            </form>

            <h1 id="manageMoviesBtn2" class="topic">Current Movies</h1>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Director</th>
                        <th>Genre</th>
                        <th>Release Year</th>
                        <th>Rating</th>
                        <th>Available</th>
                        <th>Actions</th>
                        <th>Manage</th>
                    </tr>
                </thead>
                <tbody id="movieList">
                    <?php
                    if ($movieError) {
                        echo "<tr><td colspan='8'>$movieError</td></tr>";
                    } elseif ($movies) {
                        foreach ($movies->movie as $movie) {
                            echo "<tr data-id='{$movie['id']}'>
            <td>{$movie['id']}</td>
            <td>" . htmlspecialchars($movie->title) . "</td>
            <td>" . htmlspecialchars($movie->director) . "</td>
            <td>" . htmlspecialchars($movie->genre) . "</td>
            <td>" . htmlspecialchars($movie->release_year) . "</td>
            <td>" . htmlspecialchars($movie->rating) . "</td>
            <td>" . htmlspecialchars($movie->available) . "</td>
            <td>";

                            if (isset($movie->image) && !empty($movie->image)) {
                                echo "<img src='" . htmlspecialchars($movie->image) . "' alt='" . htmlspecialchars($movie->title) . "' width='100'>";
                            } else {
                                echo "<img src='default-image.jpg' alt='No Image Available' width='100'>";
                            }

                            echo "</td>
            <td>
                <a href='#addMovieForm' id='addMovieBtn'><button class='editMovieBtn'>Edit</button></a>
                <button class='deleteMovieBtn'>Delete</button>
            </td>
        </tr>";
                        }
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <script>
            document.getElementById('manageMoviesBtn').addEventListener('click', function() {
                const target = document.getElementById('manageMoviesBtn2');
                target.scrollIntoView({
                    behavior: 'smooth'
                })
            });
        </script>

        <div id="usersSection" style="display:none;">

            <?php include 'user_manage.php'; // Include the user management file 
            ?>
        </div>

        <div id="rentalsSection" style="display:none;">

            <?php include 'manage_rentals.php'; // Include the user management file 
            ?>
        </div>
    </div>

    <script src="auth.js"></script>
    <script>
        // Toggle between Manage Movies and Manage Users
        document.getElementById('addMovieBtn').onclick = function() {
            document.getElementById('moviesSection').style.display = 'block';
            document.getElementById('usersSection').style.display = 'none';
            document.getElementById('rentalsSection').style.display = 'none';
        };

        document.getElementById('manageMoviesBtn').onclick = function() {
            document.getElementById('moviesSection').style.display = 'block';
            document.getElementById('usersSection').style.display = 'none';
            document.getElementById('rentalsSection').style.display = 'none';
        };

        document.getElementById('manageUsersBtn').onclick = function() {
            document.getElementById('usersSection').style.display = 'block';
            document.getElementById('moviesSection').style.display = 'none';
            document.getElementById('rentalsSection').style.display = 'none';
        };

        document.getElementById('manageRentalsBtn').onclick = function() {
            document.getElementById('rentalsSection').style.display = 'block';
            document.getElementById('moviesSection').style.display = 'none';
            document.getElementById('usersSection').style.display = 'none';
        };

        function deleteMovie(movieId) {
            if (confirm('Are you sure you want to delete this movie?')) {
                const xhr = new XMLHttpRequest();
                xhr.open("POST", "delete_movie.php", true);
                xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                xhr.onreadystatechange = function() {
                    if (xhr.readyState === 4 && xhr.status === 200) {
                        alert(xhr.responseText);
                        location.reload();
                    }
                };
                xhr.send(`id=${movieId}`);
            }
        }

        function editMovie(movieId) {
            const row = document.querySelector(`tr[data-id='${movieId}']`);
            const title = row.children[1].textContent;
            const director = row.children[2].textContent;
            const genre = row.children[3].textContent;
            const year = row.children[4].textContent;
            const rating = row.children[5].textContent;
            const available = row.children[6].textContent;
            const imageLink = row.querySelector("img").src;

            document.getElementById('movieId').value = movieId;
            document.getElementById('movieTitle').value = title;
            document.getElementById('movieDirector').value = director;
            document.getElementById('movieGenre').value = genre;
            document.getElementById('movieYear').value = year;
            document.getElementById('movieRating').value = rating;
            document.getElementById('movieAvailable').value = available === 'true' ? 'true' : 'false';
            document.getElementById('movieImageLink').value = imageLink;
        }

        document.getElementById('movieList').onclick = function(event) {
            if (event.target.classList.contains('deleteMovieBtn')) {
                const movieId = event.target.closest('tr').dataset.id;
                deleteMovie(movieId);
            } else if (event.target.classList.contains('editMovieBtn')) {
                const movieId = event.target.closest('tr').dataset.id;
                editMovie(movieId);
            }
        };
    </script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Listen for change event on the toggle switch
            $('.status-toggle').on('change', function() {
                var rentalId = $(this).data('rental-id'); // Get rental ID from checkbox's data attribute
                var isChecked = $(this).is(':checked'); // Check if the toggle is checked
                var newStatus = isChecked ? 'return' : 'rent'; // Determine new status

                // Perform AJAX request
                $.ajax({
                    url: 'manage_rentals.php',
                    method: 'POST',
                    data: {
                        rental_id: rentalId
                    },
                    success: function(response) {
                        // Parse JSON response
                        var result = JSON.parse(response);
                        if (result.status === 'success') {
                            // Update the status text in the row
                            $row.find('.rental-status').text(newStatus);
                            $('#message').hide(); // Hide message if successful
                        } else {
                            // Show error message
                            $('#message').text(result.message).show();
                            $(this).prop('checked', !isChecked); // Revert the toggle state
                        }
                    }.bind(this), // Bind the context to ensure 'this' refers to the checkbox
                    // Bind the context to ensure 'this' refers to the checkbox
                    error: function() {
                        alert('Failed to update status.');
                    }
                });
            });
        });
    </script>



</body>

</html>