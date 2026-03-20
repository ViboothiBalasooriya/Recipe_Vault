<?php
require_once __DIR__ . '/includes/functions.php';

$error = null;
$success = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = sanitize($_POST['title'] ?? '');
    $category = sanitize($_POST['category'] ?? '');
    $prep_time = 30; // Hidden from form based on design
    $ingredients = sanitize($_POST['ingredients'] ?? '');
    $instructions = sanitize($_POST['instructions'] ?? '');
    $user_id = $_SESSION['user_id'] ?? null;
    $image_url = '';

    // Image Upload Handling
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, $allowed)) {
            $new_name = uniqid('recipe_') . '.' . $ext;
            $upload_path = __DIR__ . '/images/uploads/' . $new_name;
            if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                $image_url = 'images/uploads/' . $new_name;
            } else {
                $error = "Failed to upload image.";
            }
        } else {
            $error = "Invalid image format. Allowed: " . implode(', ', $allowed);
        }
    }

    if (empty($error)) {
        if (empty($title) || empty($category) || empty($ingredients) || empty($instructions)) {
            $error = 'Please fill out all required recipe fields.';
        } else {
            $stmt = $conn->prepare('INSERT INTO recipes (user_id, title, category, ingredients, instructions, prep_time, image_url) VALUES (?, ?, ?, ?, ?, ?, ?)');
            $stmt->bind_param('issssis', $user_id, $title, $category, $ingredients, $instructions, $prep_time, $image_url);
            
            if ($stmt->execute()) {
                $success = 'Your recipe has been successfully submitted!';
            } else {
                $error = 'An error occurred while submitting your recipe.';
            }
            $stmt->close();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RecipeBook - Submit Recipe</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">  
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        body { background-color: #f8fafc; /* Very light cool gray */ border-top: 5px solid #198754; }
        .nav-link.active { font-weight: 700 !important; color: #333 !important; border-bottom: 2px solid #333; }
        .nav-link { color: #555; font-weight: 500; font-size: 0.95rem; }
        .recipe-logo { color: #198754; font-weight: 800; font-size: 1.3rem; text-decoration: none; }
        
        /* Steps Styling */
        .step-card { border-radius: 12px; border: 1px solid #eee; margin-bottom: 1.5rem; overflow: hidden; }
        .step-circle { width: 35px; height: 35px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 0.9rem; margin-right: 15px; }
        
        .step-1-bg { background-color: #ffffff; border: 1px solid #e2e8f0; }
        .step-1-circle { background-color: #d1e7dd; color: #0f5132; }
        
        .step-2-bg { background-color: #cfe2ff; border-color: #cfe2ff; }
        .step-2-circle { background-color: transparent; color: #084298; }
        
        /* Form Label & Controls */
        .form-label { font-size: 0.70rem; letter-spacing: 0.5px; font-weight: 800; color: #4a5568; }
        .form-control, .form-select { border: 0; background-color: #f1f5f9; border-radius: 6px; padding: 12px 15px; font-size: 0.95rem; color: #333; box-shadow: none; }
        .form-control:focus, .form-select:focus { background-color: #fff; border: 1px solid #cbd5e1; box-shadow: 0 0 0 3px rgba(25,135,84,.1); }
        
        /* Navbar Buttons */
        .btn-outline-custom { border: 1px solid #198754; color: #198754; border-radius: 20px; font-weight: 500; padding: 6px 22px; text-decoration: none; font-size: 0.9rem; }
        .btn-outline-custom:hover { background-color: #198754; color: white; }
        .btn-danger-custom { background-color: #e53e3e; color: white; border-radius: 20px; font-weight: 500; padding: 6px 22px; border: none; text-decoration: none; font-size: 0.9rem; }
        .btn-danger-custom:hover { background-color: #c53030; color: white; }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light fixed-top bg-white border-bottom py-3">
        <div class="container">
            <a class="navbar-brand fw-bold text-success fs-4" href="index.php">🍴 RecipeVault</a>

            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navMenu">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0 align-items-center">
                    <li class="nav-item"><a class="nav-link fw-medium text-dark" href="index.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link fw-medium text-dark" href="recipes.php">Recipes</a></li>
                    <li class="nav-item"><a class="nav-link fw-medium active text-success" href="submit-recipe.php">Submit Recipe</a></li>
                    <li class="nav-item"><a class="nav-link fw-medium text-dark" href="about.php">About</a></li>
                    <?php if (is_logged_in()): ?>
                        <li class="nav-item"><a class="nav-link fw-medium text-dark" href="dashboard.php">Dashboard</a></li>
                        <li class="nav-item ms-lg-3"><a class="btn btn-outline-success btn-sm px-4 rounded-pill shadow-sm mt-2 mt-lg-0" href="auth/logout.php">Logout</a></li>
                    <?php else: ?>
                        <li class="nav-item ms-lg-3"><a class="btn btn-success btn-sm px-4 rounded-pill shadow-sm mt-2 mt-lg-0" href="auth/login.php">Get Started</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

<section class="py-5">
    <div class="container px-4 px-lg-5">
        <div class="row gx-5">
            <!-- Left Content -->
            <div class="col-lg-5 pe-lg-5 mb-5 mb-lg-0 mt-3 pt-2">
                <h1 class="display-4 fw-bolder text-dark mb-0" style="letter-spacing:-1px;">Share Your</h1>
                <h1 class="display-4 fw-bolder text-success mb-4" style="letter-spacing:-1px;">Culinary Magic</h1>
                <p class="text-secondary mb-5 pe-lg-4" style="font-size: 1.15rem; line-height: 1.6;">Join our community of home chefs. Share your secret family recipes and inspire thousands of food lovers around the globe.</p>

                <div class="step-card step-1-bg p-4 d-flex align-items-center">
                    <div class="step-circle step-1-circle flex-shrink-0">1</div>
                    <div>
                        <h6 class="fw-bold text-dark mb-1" style="font-size: 1.05rem;">Fast Submission</h6>
                        <p class="text-secondary small mb-0 mt-2">Our streamlined process takes less than 5 minutes to get your recipe live.</p>
                    </div>
                </div>

                <div class="step-card step-2-bg p-4 d-flex align-items-center">
                    <div class="step-circle step-2-circle flex-shrink-0 fw-bolder">2</div>
                    <div>
                        <h6 class="fw-bold text-dark mb-1" style="font-size: 1.05rem;">Reach Thousands</h6>
                        <p class="text-secondary small mb-0 mt-2" style="color: #4a5568 !important;">Your recipes will be featured on our discovery feed and newsletter.</p>
                    </div>
                </div>
            </div>

            <!-- Right Content -->
            <div class="col-lg-7">
                <div class="card border-0 shadow rounded-4 bg-white p-4 p-md-5">
                    <?php if ($success): ?>
                        <div class="alert alert-success border-0 bg-success text-white rounded-3 small fw-bold"><i class="fas fa-check-circle me-2"></i><?php echo $success; ?></div>
                    <?php endif; ?>
                    <?php if ($error): ?>
                        <div class="alert alert-danger border-0 rounded-3 small fw-bold"><i class="fas fa-exclamation-circle me-2"></i><?php echo $error; ?></div>
                    <?php endif; ?>
                    
                    <form id="submitRecipeForm" method="POST" action="submit-recipe.php" enctype="multipart/form-data">
                        <div class="mb-4">
                            <label class="form-label text-uppercase mb-2">Recipe Image</label>
                            <input type="file" name="image" class="form-control form-control-sm" accept="image/*">
                        </div>

                        <div class="mb-4">
                            <label class="form-label text-uppercase mb-2">Recipe Title</label>
                            <input type="text" name="title" class="form-control" placeholder="e.g. Grandma's Apple Pie" required>
                        </div>

                        <div class="mb-4">
                            <label class="form-label text-uppercase mb-2">Category</label>
                            <select name="category" class="form-select text-muted" required>
                                <option value="" disabled selected>Select a category...</option>
                                <option value="Breakfast">Breakfast</option>
                                <option value="Lunch">Lunch</option>
                                <option value="Dinner">Dinner</option>
                                <option value="Dessert">Dessert</option>
                                <option value="Vegetarian">Vegetarian</option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="form-label text-uppercase mb-2">Ingredients</label>
                            <textarea name="ingredients" class="form-control" rows="4" placeholder="List ingredients separated by lines..." required></textarea>
                        </div>

                        <div class="mb-5">
                            <label class="form-label text-uppercase mb-2">Instructions</label>
                            <textarea name="instructions" class="form-control" rows="4" placeholder="Describe the steps to prepare..." required></textarea>
                        </div>

                        <button type="submit" class="btn btn-success w-100 py-3 fw-bold rounded-3 shadow-sm" style="font-size: 1.05rem;">Submit Your Recipe</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<footer class="bg-transparent py-4 mt-5">
    <div class="container text-center">
        <a href="index.php" class="text-muted text-decoration-none mx-2 small">Home</a>
        <a href="recipes.php" class="text-muted text-decoration-none mx-2 small">Recipes</a>
        <a href="submit-recipe.php" class="text-muted text-decoration-none mx-2 small">Submit</a>
        <a href="about.php" class="text-muted text-decoration-none mx-2 small">About</a>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
