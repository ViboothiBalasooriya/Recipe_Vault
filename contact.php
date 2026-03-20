<?php
require_once __DIR__ . '/includes/functions.php';

$success = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // In a real app we'd save this or email it, for now just show success.
    $success = "Thank you for reaching out! We'll get back to you soon.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Contact Us | RecipeVault</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light fixed-top py-3">
        <div class="container">
            <a class="navbar-brand fw-bold text-success" href="index.php">🍴 RecipeVault</a>
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navMenu">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0 align-items-lg-center">
                    <li class="nav-item"><a class="nav-link fw-medium" href="index.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link fw-medium" href="recipes.php">Recipes</a></li>
                    <li class="nav-item"><a class="nav-link fw-medium" href="submit-recipe.php">Submit Recipe</a></li>
                    <li class="nav-item"><a class="nav-link fw-medium" href="about.php">About</a></li>
                    <?php if (is_logged_in()): ?>
                        <li class="nav-item"><a class="nav-link fw-medium" href="dashboard.php">Dashboard</a></li>
                    <?php else: ?>
                        <li class="nav-item ms-lg-3"><a class="btn btn-success btn-sm px-4 rounded-pill shadow-sm" href="auth/login.php">Get Started</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container py-5 mt-5">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="card border-0 shadow-lg rounded-4 p-5">
                    <h2 class="fw-bold mb-4">Get in Touch</h2>
                    <p class="text-muted mb-5">Have questions or feedback? We'd love to hear from you.</p>
                    
                    <?php if ($success): ?>
                        <div class="alert alert-success"><?php echo $success; ?></div>
                    <?php endif; ?>

                    <form action="contact.php" method="POST">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Name</label>
                            <input type="text" name="name" class="form-control bg-light border-0" placeholder="Your Name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Email</label>
                            <input type="email" name="email" class="form-control bg-light border-0" placeholder="your@email.com" required>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-bold">Message</label>
                            <textarea name="message" class="form-control bg-light border-0" rows="5" placeholder="Your message..." required></textarea>
                        </div>
                        <button type="submit" class="btn btn-success w-100 py-3 shadow-sm">Send Message</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/script.js"></script>
</body>
</html>
