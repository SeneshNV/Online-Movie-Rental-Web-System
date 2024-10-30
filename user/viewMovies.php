<?php
// Initialize variables
$errorMsg = '';
$searchTitle = isset($_GET['title']) ? htmlspecialchars($_GET['title']) : '';
$searchGenre = isset($_GET['genre']) ? htmlspecialchars($_GET['genre']) : '';
$searchYear = isset($_GET['year']) ? htmlspecialchars($_GET['year']) : '';

// Load the movie XML file
$movieFile = '../movie.xml';  // Correct file name

if (file_exists($movieFile)) {
    $movies = simplexml_load_file($movieFile);
    if ($movies === false) {
        $errorMsg = "Error: Could not load the movies file.";
    }
} else {
    $errorMsg = "Error: Movie file not found.";
}
?>





<?php if ($errorMsg): ?>
    <div class="error" style="color: #febd02;">
        <?php echo $errorMsg; ?>
    </div>
<?php else: ?>

    <form method="GET" onsubmit="searchMovies(event)">
        <input type="text" name="title" placeholder="Search by Title" value="<?php echo $searchTitle; ?>">


        <select id="movieGenre" name="genre">
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

        <input type="text" name="year" placeholder="Search by Release Year" value="<?php echo $searchYear; ?>">
        <button type="submit">Search</button>
    </form>


    <div class="movie-container">
        <?php
        foreach ($movies->movie as $movie) {
            if (
                $movie->available == "true" &&
                ($searchTitle === '' || stripos($movie->title, $searchTitle) !== false) &&
                ($searchGenre === '' || stripos($movie->genre, $searchGenre) !== false) &&
                ($searchYear === '' || $movie->release_year == $searchYear)
            ) {
                echo "<div class='movie-card'>
                    <img src='{$movie->image}' alt='{$movie->title}' class='movie-image' />
                    <div class='movie-details'>
                        <h3>{$movie->title}</h3>
                        <p><strong>🎬 Director:</strong> {$movie->director}</p>
                        <p><strong>🎞 Genre:</strong> {$movie->genre}</p>
                        <p><strong>🗓 Release Year:</strong> {$movie->release_year}</p>
                        <p><strong>⭐ Rating:</strong> {$movie->rating} / 10</p>
                        <button class='rent-btn' data-movie-id='{$movie['id']}' onclick='rentMovie(\"{$movie['id']}\")'>Rent Now</button>
                    </div>
                </div>";
            }
        }
        ?>
    </div>

<?php endif; ?>