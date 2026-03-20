<?php
require_once __DIR__ . '/../includes/functions.php';

if (is_logged_in()) {
    redirect('../dashboard.php');
}

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize($_POST['username'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $email === '' || $password === '') {
        $error = 'All fields are required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters.';
    } else {
        $checkStmt = $conn->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
        $checkStmt->bind_param('s', $email);
        $checkStmt->execute();
        $exists = $checkStmt->get_result()->fetch_assoc();
        $checkStmt->close();

        if ($exists) {
            $error = 'An account with this email already exists.';
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $insertStmt = $conn->prepare('INSERT INTO users (username, email, password) VALUES (?, ?, ?)');
            $insertStmt->bind_param('sss', $username, $email, $hashedPassword);

            if ($insertStmt->execute()) {
                $insertStmt->close();
                set_flash('success', 'Registration successful. Please sign in to continue.');
                redirect('login.php');
            }

            $insertStmt->close();
            $error = 'Registration failed. Please try again.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account | RecipeVault</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
    <style>
        body, html { height: 100%; border-top: none !important; }
        .split-layout { min-height: 100vh; }
        .form-section { display: flex; flex-direction: column; justify-content: center; padding: 5rem; }
        .image-section { 
            background-image: url('https://images.unsplash.com/photo-1556910103-1c02745aae4d?auto=format&fit=crop&w=1200&q=80');
            background-size: cover; 
            background-position: center; 
            position: relative; 
        }
        .quote-overlay {
            position: absolute;
            bottom: 0; left: 0; right: 0;
            padding: 3rem;
            background: linear-gradient(to top, rgba(0,0,0,0.8) 0%, rgba(0,0,0,0) 100%);
            color: white;
        }
        .quote-text { font-size: 2rem; font-weight: 800; line-height: 1.3; margin-bottom: 1rem; }
        .quote-author { font-size: 1.1rem; opacity: 0.9; }
        
        .recipe-logo { color: #198754; font-weight: 700; font-size: 1.1rem; text-decoration: none; margin-bottom: 2rem; display: inline-block; }
        h1 { font-weight: 800; color: #0f172a; margin-bottom: 0.5rem; }
        
        /* Toggle Switch */
        .auth-toggle { background-color: #f8f9fa; border-radius: 8px; padding: 4px; display: flex; margin-bottom: 2rem; }
        .auth-toggle .btn { border: none; border-radius: 6px; padding: 10px 0; font-weight: 600; font-size: 0.95rem; }
        .auth-toggle .btn.active { background-color: #20c997; color: white; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .auth-toggle .btn.inactive { color: #6c757d; }
        .auth-toggle .btn.inactive:hover { background-color: #e9ecef; }
        
        /* Inputs */
        .form-label { font-size: 0.70rem; letter-spacing: 0.5px; font-weight: 800; color: #1e293b; text-transform: uppercase; }
        .form-control { border: 0; background-color: #f8fafc; border-radius: 6px; padding: 14px 15px; font-size: 0.95rem; color: #333; }
        .form-control:focus { background-color: #fff; border: 1px solid #198754; box-shadow: 0 0 0 3px rgba(25,135,84,.1); }
        
        .btn-success { background-color: #20c997; border-color: #20c997; border-radius: 6px; }
        .btn-success:hover { background-color: #1ba87e; border-color: #1ba87e; }
        
        .form-check-input:checked { background-color: #20c997; border-color: #20c997; }
        .form-check-label { font-size: 0.9rem; color: #475569; }
        .forgot-link { font-size: 0.9rem; color: #20c997; text-decoration: none; font-weight: 500; }
        .forgot-link:hover { text-decoration: underline; }

        @media (max-width: 991px) {
            .form-section { padding: 3rem 2rem; }
        }
    </style>
</head>
<body class="bg-white">

<div class="container-fluid p-0">
    <div class="row g-0 split-layout">
        
        <!-- Form Section -->
        <div class="col-lg-5 col-xl-4 form-section mx-auto">
            <div class="w-100" style="max-width: 420px; margin: 0 auto;">
                <a class="recipe-logo" href="../index.php"><span class="text-secondary opacity-75 me-2">🍴</span>RecipeVault</a>
                
                <h1 class="h2">Create Account</h1>
                <p class="text-muted mb-4 pb-2" style="font-size: 0.95rem;">Join our community of food enthusiasts today.</p>
                
                <?php if ($error): ?>
                    <div class="alert alert-danger border-0 small fw-bold"><i class="fas fa-exclamation-circle me-2"></i><?php echo $error; ?></div>
                <?php endif; ?>

                <div class="auth-toggle">
                    <a href="login.php" class="btn inactive w-50">Login</a>
                    <a href="register.php" class="btn active w-50">Sign Up</a>
                </div>

                <form method="POST" id="registerForm" novalidate>
                    <div class="mb-4">
                        <label class="form-label mb-2">Full Name</label>
                        <input class="form-control" type="text" name="username" placeholder="John Doe" required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label mb-2">Email Address</label>
                        <input class="form-control" type="email" name="email" placeholder="chef@example.com" required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label mb-2">Password</label>
                        <input class="form-control" type="password" name="password" placeholder="••••••••" minlength="6" required>
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="rememberMe">
                            <label class="form-check-label" for="rememberMe">Remember me</label>
                        </div>
                        <a href="#" class="forgot-link">Forgot password?</a>
                    </div>
                    
                    <button class="btn btn-success w-100 py-3 fw-bold mb-4" type="submit">Join RecipeBook</button>
                    
                    <p class="text-center small text-muted mb-0">Already have an account? <a class="text-success fw-bold text-decoration-none" href="login.php" style="color: #20c997 !important;">Sign In</a></p>
                </form>
            </div>
        </div>
        
        <!-- Image Section -->
        <div class="col-lg-7 col-xl-8 d-none d-lg-block image-section">
            <div class="quote-overlay">
                <blockquote class="mb-0">
                    <p class="quote-text">"Cooking is like love. It should be entered into with abandon or not at all."</p>
                    <footer class="quote-author">— Harriet van Horne</footer>
                </blockquote>
            </div>
        </div>
        
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
