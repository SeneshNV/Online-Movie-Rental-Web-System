<h1 class="topic">Current Users</h1>

<table id="userTable" border="1" cellpadding="10">
    <thead>
        <tr>
            <th>Username</th>
            <th>User ID</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php
        if ($userError) {
            echo "<tr><td colspan='3'>$userError</td></tr>";
        } elseif ($users) {
            foreach ($users->user as $user) {
                $username = trim((string)$user->username); // Get username without extra spaces
                $userId = (string)$user['id']; // Access user ID from the attribute

                echo "<tr>
                        <td>" . htmlspecialchars($username) . "</td>
                        <td>" . htmlspecialchars($userId) . "</td>
                        <td><button onclick=\"deleteUser('" . htmlspecialchars($userId) . "')\">Delete User</button></td>
                      </tr>";
            }
        }
        ?>
    </tbody>
</table>

<script>
    function deleteUser(userId) {
        if (confirm('Are you sure you want to delete this user?')) {
            const xhr = new XMLHttpRequest();
            xhr.open("POST", "delete_user.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    alert(xhr.responseText);
                    location.reload(); // Reload the page to reflect changes
                }
            };
            xhr.send(`id=${userId}`);
        }
    }
</script>