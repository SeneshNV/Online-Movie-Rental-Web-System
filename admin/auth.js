const addMovie = (event) => {
  event.preventDefault(); // Prevent default form submission

  const form = document.getElementById("addMovieForm");
  const formData = new FormData(form);

  const title = document.getElementById("movieTitle").value;
  const director = document.getElementById("movieDirector").value;
  const genre = document.getElementById("movieGenre").value;
  const year = document.getElementById("movieYear").value;
  const rating = document.getElementById("movieRating").value;
  const available = document.getElementById("movieAvailable").value;
  const imageLink = document.getElementById("movieImageLink").value;
  const movieId = document.getElementById("movieId").value; // Get the movieId

  // AJAX request to add or edit the movie with the image link
  const xhr = new XMLHttpRequest();
  xhr.open("POST", "add_movie.php", true);
  xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

  xhr.onreadystatechange = function () {
    if (xhr.readyState === 4 && xhr.status === 200) {
      alert(xhr.responseText); // Display server response
      location.reload(); // Reload the page to see the updated movie list
    }
  };

  // Include movieId in the request, if available
  xhr.send(
    `title=${encodeURIComponent(title)}&director=${encodeURIComponent(
      director
    )}&genre=${encodeURIComponent(genre)}&year=${encodeURIComponent(
      year
    )}&rating=${encodeURIComponent(rating)}&available=${encodeURIComponent(
      available
    )}&imageLink=${encodeURIComponent(imageLink)}&movieId=${encodeURIComponent(
      movieId
    )}`
  );
};

// Attach the add/edit movie function
document.getElementById("addMovieForm")?.addEventListener("submit", addMovie);
