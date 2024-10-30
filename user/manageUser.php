<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION['user_id'])) {
  echo json_encode(['status' => 'error', 'message' => 'You must be logged in to manage your account!']);
  exit;
}

$xmlFile = '../users.xml';

$xml = simplexml_load_file($xmlFile) or die(json_encode(['status' => 'error', 'message' => 'Error: Cannot load XML file.']));

$currentUserId = $_SESSION['user_id'];
$currentUser = null;

foreach ($xml->user as $user) {
  if ((string)$user['id'] === $currentUserId) {
    $currentUser = $user;
    break;
  }
}

if (!$currentUser) {
  echo json_encode(['status' => 'error', 'message' => 'User not found!']);
  exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  // Handle Username Update
  if (isset($_POST['update_username'])) {
    $newUsername = htmlspecialchars(trim($_POST['username']));

    // Check if username already exists
    foreach ($xml->user as $user) {
      if ($user->username == $newUsername) {
        echo json_encode(['status' => 'error', 'message' => 'Username already exists!']);
        exit;
      }
    }

    // Update the username
    $currentUser->username = $newUsername;
    if ($xml->asXML($xmlFile)) {
      // Update the session username
      $_SESSION['username'] = $newUsername;
      echo json_encode(['status' => 'success', 'message' => 'Username updated successfully!']);
    } else {
      echo json_encode(['status' => 'error', 'message' => 'Failed to save XML file!']);
    }
    exit;
  }

  // Handle Password Change
  if (isset($_POST['change_password'])) {
    $oldPassword = htmlspecialchars(trim($_POST['old_password']));
    $newPassword = htmlspecialchars(trim($_POST['new_password']));

    // Verify the current password using password_verify()
    if (!password_verify($oldPassword, (string)$currentUser->password)) {
      echo json_encode(['status' => 'error', 'message' => 'Current password is incorrect!']);
    } else {
      // Hash the new password
      $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

      // Update the user's password with the hashed password
      $currentUser->password = $hashedPassword;
      if ($xml->asXML($xmlFile)) {
        echo json_encode(['status' => 'success', 'message' => 'Password changed successfully!']);
      } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to save XML file!']);
      }
    }
    exit;
  }
}
?>

<body>
  <div class="container">
    <h1 class="topic">Manage User Account</h1>

    <div class="user-manage-forms">
      <!-- Form to update username -->
      <form class="form-container" id="update-username-form" onsubmit="submitForm(event, 'update-username-form', 'manageUser.php')">
        <fieldset class="form-fieldset">
          <h3>Update Username</h3>
          <div class="form-group">
            <label class="form-label" for="username">New Username</label>
            <input class="form-input" type="text" id="username" name="username" placeholder="Enter your new username" required>
          </div>
          <input type="hidden" name="update_username" value="1">
          <button class="rent-btn" type="submit">Update Username</button>
        </fieldset>
      </form>

      <!-- Form to change password -->
      <form class="form-container" id="change-password-form" onsubmit="submitForm(event, 'change-password-form', 'manageUser.php')">
        <fieldset class="form-fieldset">
          <h3>Change Password</h3>
          <div class="form-group">
            <label class="form-label" for="old_password">Current Password</label>
            <input class="form-input" type="password" id="old_password" name="old_password" placeholder="Enter current password" required>
          </div>
          <div class="form-group">
            <label class="form-label" for="new_password">New Password</label>
            <input class="form-input" type="password" id="new_password" name="new_password" placeholder="Enter new password" required>
          </div>
          <input type="hidden" name="change_password" value="1">
          <button class="rent-btn" type="submit">Change Password</button>
        </fieldset>
      </form>

    </div>

    <!-- Display messages -->
    <div id="response-message" class="message"></div>
  </div>

  <script>
    // General form submission handler for dynamic forms
    function handleFormSubmit(event, formId) {
      event.preventDefault();
      const formData = new FormData(document.getElementById(formId));

      fetch('manageUser.php', {
          method: 'POST',
          body: formData
        })
        .then(response => response.json())
        .then(data => {
          displayMessage(data);
        })
        .catch(error => console.error('Error:', error));
    }

    // Display messages
    function displayMessage(data) {
      const messageDiv = document.getElementById('response-message');
      messageDiv.textContent = data.message;
      messageDiv.classList.remove('error', 'success');

      if (data.status === 'success') {
        messageDiv.classList.add('success');
      } else {
        messageDiv.classList.add('error');
      }
    }

    // Attach event listeners to forms
    document.getElementById('update-username-form').addEventListener('submit', function(event) {
      handleFormSubmit(event, 'update-username-form');
    });

    document.getElementById('change-password-form').addEventListener('submit', function(event) {
      handleFormSubmit(event, 'change-password-form');
    });
  </script>
</body>

</html>