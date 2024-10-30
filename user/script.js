document.addEventListener("DOMContentLoaded", () => {
  const usernameForm = document.getElementById("username-form");
  const passwordForm = document.getElementById("password-form");

  usernameForm.addEventListener("submit", async (e) => {
    e.preventDefault();
    const formData = new FormData(usernameForm);
    const response = await fetch("userManage.php", {
      method: "POST",
      body: formData,
    });
    const data = await response.json();
    alert(data.success || data.error);
  });

  passwordForm.addEventListener("submit", async (e) => {
    e.preventDefault();
    const formData = new FormData(passwordForm);
    const response = await fetch("userManage.php", {
      method: "POST",
      body: formData,
    });
    const data = await response.json();
    alert(data.success || data.error);
  });
});
