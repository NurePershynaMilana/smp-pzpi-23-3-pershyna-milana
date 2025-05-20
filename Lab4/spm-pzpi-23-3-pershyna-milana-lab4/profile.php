<?php
// Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    // If not logged in, redirect to login page
    header('Location: main.php?page=login');
    exit;
}

// Initialize user profile data or load from session if exists
if (!isset($_SESSION['profile'])) {
    $_SESSION['profile'] = [
        'name' => '',
        'surname' => '',
        'birthday' => '',
        'brief_description' => '',
        'photo' => ''
    ];
}

$profile = $_SESSION['profile'];

// Process profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_profile'])) {
    $errors = [];
    
    // Validate name
    if (empty($_POST['name'])) {
        $errors[] = "Name is required";
    }
    
    // Validate surname
    if (empty($_POST['surname'])) {
        $errors[] = "Surname is required";
    } elseif (str_word_count($_POST['surname']) < 2) {
        $errors[] = "Surname must contain at least 2 words";
    }
    
    // Validate brief description
    if (empty($_POST['brief_description'])) {
        $errors[] = "Brief description is required";
    } elseif (strlen($_POST['brief_description']) < 30) {
        $errors[] = "Brief description must be at least 30 characters";
    }
    
    // Simple age check (must be at least 16)
    if (!empty($_POST['birthday'])) {
        $birthday = new DateTime($_POST['birthday']);
        $now = new DateTime();
        $age = $now->diff($birthday)->y;
        
        if ($age < 16) {
            $errors[] = "You must be at least 16 years old";
        }
    } else {
        $errors[] = "Birthday is required";
    }
    
    // Photo upload handling (in a real application)
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === 0) {
        // File upload simulation - in a real application, you would:
        // 1. Check file type (mime)
        // 2. Check file size
        // 3. Move the file to a permanent location
        // 4. Save the file path in the user profile
        
        // For simplicity, we just record that a photo was uploaded
        $_SESSION['profile']['photo'] = 'photo_uploaded.jpg';
    }
    
    // If no errors, save profile
    if (empty($errors)) {
        $_SESSION['profile'] = [
            'name' => $_POST['name'],
            'surname' => $_POST['surname'],
            'birthday' => $_POST['birthday'],
            'brief_description' => $_POST['brief_description'],
            'photo' => $_SESSION['profile']['photo']
        ];
        
        $success_message = "Profile updated successfully";
    }
}
?>

<h2>User Profile</h2>

<?php if (isset($errors) && !empty($errors)): ?>
    <div style="background-color: #ffeeee; color: #cc0000; padding: 10px; margin-bottom: 15px; border: 1px solid #cc0000;">
        <ul style="margin: 0; padding-left: 20px;">
            <?php foreach ($errors as $error): ?>
                <li><?= $error ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<?php if (isset($success_message)): ?>
    <div style="background-color: #eeffee; color: #006600; padding: 10px; margin-bottom: 15px; border: 1px solid #006600;">
        <?= $success_message ?>
    </div>
<?php endif; ?>

<div style="display: flex; flex-wrap: wrap; gap: 20px;">
    <div style="flex: 1; min-width: 300px;">
        <form method="POST" action="main.php?page=profile" enctype="multipart/form-data">
            <div style="margin-bottom: 15px;">
                <label for="name">Name:</label><br>
                <input type="text" id="name" name="name" value="<?= htmlspecialchars($profile['name']) ?>" 
                       style="width: 100%; padding: 8px; box-sizing: border-box;">
            </div>
            
            <div style="margin-bottom: 15px;">
                <label for="surname">Surname:</label><br>
                <input type="text" id="surname" name="surname" value="<?= htmlspecialchars($profile['surname']) ?>" 
                       style="width: 100%; padding: 8px; box-sizing: border-box;">
            </div>
            
            <div style="margin-bottom: 15px;">
                <label for="birthday">Birthday:</label><br>
                <input type="date" id="birthday" name="birthday" value="<?= htmlspecialchars($profile['birthday']) ?>" 
                       style="width: 100%; padding: 8px; box-sizing: border-box;">
            </div>
            
            <div style="margin-bottom: 15px;">
                <label for="brief_description">Brief description:</label><br>
                <textarea id="brief_description" name="brief_description" 
                          style="width: 100%; padding: 8px; box-sizing: border-box; height: 100px;"><?= htmlspecialchars($profile['brief_description']) ?></textarea>
            </div>
            
            <div style="margin-bottom: 15px;">
                <label for="photo">Photo:</label><br>
                <input type="file" id="photo" name="photo" accept="image/*" 
                       style="width: 100%; padding: 8px; box-sizing: border-box;">
            </div>
            
            <div>
                <button type="submit" name="save_profile" value="1" class="button">Save</button>
            </div>
        </form>
    </div>
    
    <div style="flex: 1; min-width: 300px; border: 1px solid #ddd; padding: 15px;">
        <h3>Profile Preview</h3>
        
        <div style="display: flex; align-items: flex-start; margin-bottom: 15px;">
            <div style="width: 150px; height: 150px; background-color: #eee; margin-right: 15px; display: flex; align-items: center; justify-content: center;">
                <?php if (!empty($profile['photo'])): ?>
                    <span>Photo uploaded</span>
                <?php else: ?>
                    <span>No photo</span>
                <?php endif; ?>
            </div>
            
            <div>
                <p><strong>Name:</strong> <?= htmlspecialchars($profile['name']) ?></p>
                <p><strong>Surname:</strong> <?= htmlspecialchars($profile['surname']) ?></p>
                <p><strong>Birthday:</strong> <?= htmlspecialchars($profile['birthday']) ?></p>
            </div>
        </div>
        
        <div>
            <h4>Description:</h4>
            <p><?= nl2br(htmlspecialchars($profile['brief_description'])) ?></p>
        </div>
    </div>
</div>
