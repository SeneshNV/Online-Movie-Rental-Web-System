<?php
session_start();

if (!isset($_SESSION['username'])) {
  header("Location: ../login.html");
  exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="user-styles.css">
  <title>User Home Page</title>
</head>

<body>
  <header>
    <h1 onclick="loadContent('viewMovies.php')">Online Movie Rental</h1>
    <nav>
      <ul>
        <li><a href="#" onclick="loadContent('viewMovies.php')">Home</a></li>
        <li><a href="#" onclick="loadContent('rentalHistory.php')">View Rental History</a></li>
        <li><a href="#" onclick="loadContent('returnHistory.php')">Return Movies</a></li>
        <li><a href="#" onclick="loadContent('manageUser.php')">Manage Profile</a></li>
        <li><a href="../logout.php">Logout</a></li>
        <li><a class="name" onclick="loadContent('manageUser.php')">Hi, <?php echo $_SESSION['username']; ?>!</a></li>
      </ul>
    </nav>
    <div class="hamburger" onclick="toggleMenu()">
      <div></div>
      <div></div>
      <div></div>
    </div>
  </header>

  <div id="content">
    <?php include 'viewMovies.php'; ?>
  </div>

  <footer>
    <p>&copy; 2024 Movie Rental System</p>
    <p>Dev by: Senesh NV</p>
  </footer>

  <!-- Popup Message Box -->
  <div class="popup" id="popup">
    <div class="popup-content">
      <p id="popup-message" class="message-error"></p>
      <button onclick="closePopup()">Close</button>
    </div>
  </div>

  <script>
    // Toggle mobile navigation menu
    function toggleMenu() {
      const navLinks = document.querySelector('nav ul');
      navLinks.classList.toggle('show');
    }

    // Dynamically load content into the #content div
    function loadContent(page) {
      fetch(page)
        .then(response => {
          if (!response.ok) {
            throw new Error('Network response was not ok');
          }
          return response.text();
        })
        .then(html => {
          document.getElementById('content').innerHTML = html;
        })
        .catch(error => console.error('Error loading content:', error));
    }



    // Rent a movie with form submission protection
    let isSubmitting = false;

    function rentMovie(movieId) {
      if (isSubmitting) return;
      isSubmitting = true;

      fetch('rentMovie.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
          },
          body: 'movieId=' + encodeURIComponent(movieId)
        })
        .then(response => response.json())
        .then(data => {
          document.getElementById('popup-message').innerText = data.message;
          document.getElementById('popup').style.display = "flex";
        })
        .catch(error => console.error('Error:', error))
        .finally(() => isSubmitting = false);
    }

    /// AJAX function to handle form submission and display search results
    function searchMovies(event) {
      event.preventDefault(); // Prevent form from submitting normally
      const form = event.target;

      // Get form values
      const formData = new FormData(form);
      const params = new URLSearchParams(formData).toString();

      // Make AJAX request to viewMovies.php with search parameters
      const xhr = new XMLHttpRequest();
      xhr.open('GET', 'viewMovies.php?' + params, true);
      xhr.onload = function() {
        if (xhr.status === 200) {
          document.getElementById('content').innerHTML = xhr.responseText;
        } else {
          console.error("Error loading search results:", xhr.statusText);
        }
      };
      xhr.onerror = function() {
        console.error("Search request failed.");
      };
      xhr.send();
    }

    // General form submission handler for dynamic forms
    function submitForm(event, formId, actionUrl) {
      event.preventDefault();

      const form = document.getElementById(formId);
      const formData = new FormData(form);

      // Perform the fetch request
      fetch(actionUrl, {
          method: 'POST',
          body: formData
        })
        .then(response => response.json())
        .then(data => {
          if (data.status === 'success') {
            // Display success message
            displayPopupMessage('Success', data.message);
          } else {
            // Display error message
            displayPopupMessage('Error', data.message);
          }
        })
        .catch(error => {
          console.error('Error:', error);
          displayPopupMessage('Error', 'An unexpected error occurred.');
        });
    }

    // Display popup message function
    function displayPopupMessage(type, message) {
      const popupMessage = document.getElementById('popup-message');
      popupMessage.innerText = `${type}: ${message}`;
      popupMessage.className = `message-${type.toLowerCase()}`;

      // Show popup
      document.getElementById('popup').style.display = 'flex';
    }

    // Close popup message
    function closePopup() {
      document.getElementById('popup').style.display = 'none';
    }
  </script>

</body>

</html>