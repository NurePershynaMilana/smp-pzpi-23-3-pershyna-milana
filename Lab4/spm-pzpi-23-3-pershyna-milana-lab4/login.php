<?php
// Check if form submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Simple validation
    if (!empty($_POST['username']) && !empty($_POST['password'])) {
        // In a real application, you would verify against a database
        // For this example, we use a static array as mentioned in the task
        $credentials = [
            'userName' => 'Test',
            'password' => '123123'
        ];
        
        // Check credentials
        if ($_POST['username'] === $credentials['userName'] && 
            $_POST['password'] === $credentials['password']) {
            
            // Login successful
            $_SESSION['logged_in'] = true;
            $_SESSION['username'] = $_POST['username'];
            $_SESSION['login_time'] = date('Y-m-d H:i:s');
            
            // Redirect to products page
            header('Location: main.php?page=products');
            exit;
        } else {
            $error_message = "Invalid username or password";
        }
    } else {
        $error_message = "Please fill in all fields";
    }
}
?>

<div style="max-width: 400px; margin: 0 auto; padding: 20px;">
    <h2>Login</h2>
    
    <?php if (isset($error_message)): ?>
        <div style="background-color: #ffeeee; color: #cc0000; padding: 10px; margin-bottom: 15px; border: 1px solid #cc0000;">
            <?= $error_message ?>
        </div>
    <?php endif; ?>
    
    <form method="POST" action="main.php?page=login">
        <div style="margin-bottom: 15px;">
            <label for="username">User Name:</label><br>
            <input type="text" id="username" name="username" style="width: 100%; padding: 8px; box-sizing: border-box;">
        </div>
        
        <div style="margin-bottom: 15px;">
            <label for="password">Password:</label><br>
            <input type="password" id="password" name="password" style="width: 100%; padding: 8px; box-sizing: border-box;">
        </div>
        
        <div>
            <button type="submit" class="button" style="width: 100%;">Login</button>
        </div>
    </form>
</div>
