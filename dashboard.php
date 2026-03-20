<?php
require_once __DIR__ . '/includes/functions.php';

// Ensure user is logged in
require_login();

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'] ?? 'Chef';

// Fetch user's submitted recipes
$recipes = [];
$stmt = $conn->prepare("SELECT * FROM recipes WHERE user_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $recipes[] = $row;
    }
}
$stmt->close();

$submitted_count = count($recipes);

// Generate initials for avatar
$initials = strtoupper(substr($username, 0, 2));

// Delete logic (basic implementation)
if (isset($_GET['delete_id'])) {
    $del_id = (int)$_GET['delete_id'];
    $del_stmt = $conn->prepare("DELETE FROM recipes WHERE id = ? AND user_id = ?");
    $del_stmt->bind_param("ii", $del_id, $user_id);
    $del_stmt->execute();
    $del_stmt->close();
    redirect('dashboard.php');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RecipeBook - My Profile</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">  
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        body { background-color: #fcfcfc; }
        .recipe-logo { color: #198754; font-weight: 800; font-size: 1.3rem; text-decoration: none; }
        .nav-link { color: #555; font-weight: 500; font-size: 0.95rem; }
        
        /* Profile Header */
        .avatar-circle { width: 100px; height: 100px; background-color: #3f5159; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 2.2rem; font-weight: 500; margin: 0 auto; position: relative; }
        .camera-badge { position: absolute; bottom: 0; right: 0; background-color: white; color: #198754; width: 28px; height: 28px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.8rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1); cursor: pointer; border: 2px solid white; }
        .profile-greeting { font-size: 2.5rem; font-weight: 800; color: #111; margin-top: 1.5rem; }
        .profile-username { color: #198754; }
        .profile-bio { color: #666; font-size: 1.05rem; max-width: 500px; margin: 0 auto; line-height: 1.6; }
        
        .stat-value { font-size: 1.5rem; font-weight: 800; color: #111; margin-bottom: 0px; }
        .stat-label { font-size: 0.65rem; font-weight: 700; color: #888; letter-spacing: 0.5px; text-transform: uppercase; }
        
        /* Recipe Cards */
        .section-title { font-size: 2.2rem; font-weight: 800; color: #111; letter-spacing: -0.5px; }
        .recipe-card { border: none; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.04); background-color: #fff; transition: transform 0.2s; }
        .recipe-card:hover { transform: translateY(-3px); }
        .card-img-wrapper { position: relative; height: 220px; overflow: hidden; background-color: #f8f9fa; }
        .card-img-wrapper img { width: 100%; height: 100%; object-fit: cover; }
        .approved-badge { position: absolute; top: 15px; right: 15px; background-color: #198754; color: white; padding: 4px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; }
        
        .card-title { font-weight: 800; font-size: 1.25rem; color: #222; margin-top: 10px; margin-bottom: 20px; }
        .card-footer-custom { border-top: 1px solid #eee; padding-top: 15px; display: flex; justify-content: space-between; align-items: center; }
        .added-date { font-size: 0.8rem; color: #777; font-weight: 500; }
        .action-links a { text-decoration: none; font-size: 0.9rem; font-weight: 600; margin-left: 15px; }
        .btn-edit { color: #0d6efd; }
        .btn-delete { color: #dc3545; }
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
                    <li class="nav-item"><a class="nav-link fw-medium text-dark" href="submit-recipe.php">Submit Recipe</a></li>
                    <li class="nav-item"><a class="nav-link fw-medium text-dark" href="about.php">About</a></li>
                    <?php if (is_logged_in()): ?>
                        <li class="nav-item"><a class="nav-link fw-medium active text-success" href="dashboard.php">Dashboard</a></li>
                        <li class="nav-item ms-lg-3"><a class="btn btn-outline-success btn-sm px-4 rounded-pill shadow-sm mt-2 mt-lg-0" href="auth/logout.php">Logout</a></li>
                    <?php else: ?>
                        <li class="nav-item ms-lg-3"><a class="btn btn-success btn-sm px-4 rounded-pill shadow-sm mt-2 mt-lg-0" href="auth/login.php">Get Started</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

<section class="py-5 text-center">
    <div class="container">
        <div class="avatar-circle">
            <?php echo $initials; ?>
            <div class="camera-badge">
                <i class="fas fa-camera"></i>
            </div>
        </div>
        
        <h1 class="profile-greeting">Hello, <span class="profile-username"><?php echo htmlspecialchars($username); ?></span>!</h1>
        <p class="profile-bio mt-3">Culinary enthusiast. Loves experimenting with new flavors and sharing the joy of cooking with the world.</p>
        
        <div class="d-flex justify-content-center gap-5 mt-4 pt-2">
            <div>
                <p class="stat-value"><?php echo $submitted_count; ?></p>
                <p class="stat-label">Submitted Recipes</p>
            </div>
            <div>
                <p class="stat-value">0</p>
                <p class="stat-label">Saved Favorites</p>
            </div>
            <div>
                <p class="stat-value">250</p>
                <p class="stat-label">Profile Views</p>
            </div>
        </div>
    </div>
</section>

<section class="py-5 bg-transparent">
    <div class="container px-4 px-lg-5">
        <div class="d-flex justify-content-between align-items-end mb-4 pb-2">
            <div>
                <h2 class="section-title mb-1">My Submitted Recipes</h2>
                <p class="text-secondary small mb-0">Manage and view the culinary masterpieces you've shared.</p>
            </div>
            <a href="submit-recipe.php" class="btn btn-success rounded-pill px-4 py-2 fw-bold shadow-sm d-flex align-items-center gap-2">
                <i class="fas fa-plus"></i> Submit New
            </a>
        </div>

        <div class="row g-4">
            <?php if (empty($recipes)): ?>
                <div class="col-12 text-center text-muted py-5">
                    <p class="lead">You haven't submitted any recipes yet.</p>
                    <a href="submit-recipe.php" class="btn btn-outline-success">Start Sharing Now</a>
                </div>
            <?php else: ?>
                <?php foreach ($recipes as $recipe): ?>
                    <?php 
                        $imgUrl = !empty($recipe['image_url']) ? htmlspecialchars($recipe['image_url']) : "https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&w=800&q=80";
                        $addedDate = date('M d, Y', strtotime($recipe['created_at']));
                    ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="card recipe-card h-100">
                            <div class="card-img-wrapper">
                                <img src="<?php echo $imgUrl; ?>" alt="<?php echo htmlspecialchars($recipe['title']); ?>">
                                <div class="approved-badge">Approved</div>
                            </div>
                            <div class="card-body p-4 pb-3 flex-column d-flex justify-content-between">
                                <h4 class="card-title"><?php echo htmlspecialchars($recipe['title']); ?></h4>
                                <div class="card-footer-custom mt-auto">
                                    <span class="added-date">Added: <?php echo $addedDate; ?></span>
                                    <div class="action-links d-flex align-items-center">
                                        <a href="#" class="btn-edit d-flex align-items-center gap-1"><i class="fas fa-edit"></i> Edit</a>
                                        <a href="dashboard.php?delete_id=<?php echo $recipe['id']; ?>" class="btn-delete ms-3" onclick="return confirm('Are you sure you want to delete this recipe?');"><i class="fas fa-trash"></i></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
