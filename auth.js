document.addEventListener("DOMContentLoaded", function () {
  const registerForm = document.getElementById("registerForm");
  const loginForm = document.getElementById("loginForm");
  const adminLoginForm = document.getElementById("adminLoginForm");

  if (registerForm) {
    registerForm.addEventListener("submit", function (event) {
      event.preventDefault();
      const username = document.getElementById("username").value;
      const password = document.getElementById("password").value;
      const confirmPassword = document.getElementById("confirmPassword").value;
      const message = document.getElementById("message");

      if (password !== confirmPassword) {
        message.textContent = "Passwords do not match!";
        return;
      }

      const xhr = new XMLHttpRequest();
      xhr.open("POST", "register.php", true);
      xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

      xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
          message.textContent = xhr.responseText;
        }
      };

      xhr.send(
        `username=${encodeURIComponent(username)}&password=${encodeURIComponent(
          password
        )}`
      );
    });
  }

  if (loginForm) {
    loginForm.addEventListener("submit", function (event) {
      event.preventDefault();
      const username = document.getElementById("username").value;
      const password = document.getElementById("password").value;
      const message = document.getElementById("message");

      const xhr = new XMLHttpRequest();
      xhr.open("POST", "login.php", true);
      xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

      xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
          const response = JSON.parse(xhr.responseText);
          if (response.success) {
            window.location.href = "user/home.php"; // Redirect to user home page
          } else {
            message.textContent = response.message;
          }
        }
      };

      xhr.send(
        `username=${encodeURIComponent(username)}&password=${encodeURIComponent(
          password
        )}`
      );
    });
  }

  if (adminLoginForm) {
    adminLoginForm.addEventListener("submit", function (event) {
      event.preventDefault();
      const username = document.getElementById("username").value;
      const password = document.getElementById("password").value;
      const message = document.getElementById("message");

      const xhr = new XMLHttpRequest();
      xhr.open("POST", "login.php", true);
      xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

      xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
          const response = JSON.parse(xhr.responseText);
          if (response.success) {
            window.location.href = "admin-home.php";
          } else {
            message.textContent = response.message;
          }
        }
      };

      xhr.send(
        `username=${encodeURIComponent(username)}&password=${encodeURIComponent(
          password
        )}`
      );
    });
  }
});
