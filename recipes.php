<?php
require_once __DIR__ . '/includes/functions.php';

$recipes = [];
$result = $conn->query("SELECT * FROM recipes ORDER BY created_at DESC");

if ($result && $result->num_rows === 0) {
    // Auto-seed dummy recipes
    $conn->query("INSERT INTO users (username, email, password) VALUES ('demo_chef', 'demo@recipevault.com', 'dummy')");
    $user_id = $conn->insert_id ?? 1;
    
    $dummy_recipes = [
        ["Classic Honey Pancakes", "Breakfast", "2 cups Flour\n1 cup Milk\n2 Eggs", "1. Mix\n2. Cook", "https://images.unsplash.com/photo-1567620905732-2d1ec7ab7445", 20],
        ["Signature Harvest Bowl", "Vegetarian", "Kale\nQuinoa\nTahini", "1. Roast\n2. Mix", "https://images.unsplash.com/photo-1546069901-ba9599a7e63c", 15],
        ["Dark Chocolate Lava", "Dessert", "Chocolate\nButter\nEggs", "1. Melt\n2. Bake", "https://images.unsplash.com/photo-1624353365286-3f8d62daad51", 40],
        ["Creamy Mushroom Risotto", "Dinner", "Rice\nMushrooms", "1. Sauté\n2. Cook", "https://images.unsplash.com/photo-1476124369491-e7addf5db371", 45],
        ["Spicy Thai Green Curry", "Dinner", "Curry Paste\nCoconut", "1. Fry\n2. Simmer", "https://images.unsplash.com/photo-1455619452474-d2be8b1e70cd", 30],
        ["Summer Berry Tart", "Dessert", "Pastry\nBerries", "1. Bake\n2. Fill", "https://images.unsplash.com/photo-1519915028121-7d3463d20b13", 60]
    ];
    
    $stmt = $conn->prepare("INSERT INTO recipes (user_id, title, category, ingredients, instructions, image_url, prep_time) VALUES (?, ?, ?, ?, ?, ?, ?)");
    foreach ($dummy_recipes as $dr) {
        $stmt->bind_param("isssssi", $user_id, $dr[0], $dr[1], $dr[2], $dr[3], $dr[4], $dr[5]);
        $stmt->execute();
    }
    $stmt->close();
    header("Location: recipes.php");
    exit;
}

// Fetch from DB
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $recipes[] = $row;
    }
}

// Prepare Data for the Javascript Modal Array to replace script.js defaults
$dynamicRecipesData = [];
foreach ($recipes as $r) {
    $dynamicRecipesData[] = [
        'title' => htmlspecialchars($r['title']),
        'category' => htmlspecialchars($r['category']),
        'desc' => "Flavorful {$r['category']} prepared in {$r['prep_time']} minutes.",
        'ingredients' => array_filter(array_map('trim', explode("\n", $r['ingredients']))),
        'steps' => htmlspecialchars($r['instructions']),
        'img' => !empty($r['image_url']) ? htmlspecialchars($r['image_url']) : 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c'
    ];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RecipeVault - Recipes</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <script>
        window.dynamicRecipesData = <?php echo json_encode($dynamicRecipesData); ?>;
    </script>
</head>
<body>
  <nav class="navbar navbar-expand-lg navbar-light fixed-top py-3">
        <div class="container">
            <a class="navbar-brand fw-bold text-success" href="index.php">🍴 RecipeVault</a>

            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navMenu">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                    <li class="nav-item"><a class="nav-link fw-medium " href="index.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link fw-medium active" href="recipes.php">Recipes</a></li>
                    <li class="nav-item"><a class="nav-link fw-medium" href="submit-recipe.php">Submit Recipe</a></li>
                    <li class="nav-item"><a class="nav-link fw-medium " href="about.php">About</a></li>
                    <?php if (is_logged_in()): ?>
                        <li class="nav-item"><a class="nav-link fw-medium" href="dashboard.php">Dashboard</a></li>
                        <li class="nav-item ms-lg-3"><a class="btn btn-outline-success btn-sm px-4 rounded-pill shadow-sm mt-2 mt-lg-0" href="auth/logout.php">Logout</a></li>
                    <?php else: ?>
                        <li class="nav-item ms-lg-3"><a class="btn btn-success btn-sm px-4 rounded-pill shadow-sm mt-2 mt-lg-0" href="auth/login.php">Get Started</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
<header class="bg-light py-5 mt-5">
    <div class="container py-5 text-center reveal">
        <h1 class="display-3 fw-bold md-3">Explore Our<span class="text-success"> Recipes</span></h1>
        <p class="lead text-muted mx-auto" style="max-width:600px;">From quick breakfast sancks to elaborate gourmet dinners,discover a world of flavors curated by our global community.</p>
    </div>

</header>
  <section class="container py-4">
        <div class="row g-3">
            <div class="col-md-8">
                <input type="text" id="searchInput"
                    class="form-control form-control-lg bg-white border-0 shadow-sm px-4"
                    placeholder="Search for ingredients, dishes, or chefs...">
            </div>
            <div class="col-md-4">
                <select id="categoryFilter" class="form-select form-select-lg bg-white border-0 shadow-sm px-4">
                    <option value="All" selected>All Categories</option>
                    <option value="Breakfast">Breakfast</option>
                    <option value="Dinner">Dinner</option>
                    <option value="Vegetarian">Vegetarian</option>
                    <option value="Dessert">Dessert</option>
                </select>
            </div>
        </div>
    </section>

<!--recipe grid-->
    <section class="container py-5">
        <div class="row g-4">
            <?php if (empty($recipes)): ?>
                <div class="col-12 text-center text-muted">
                    <p>No recipes found in the database. <a href="submit-recipe.php" class="text-success text-decoration-none fw-bold">Be the first to submit one!</a></p>
                </div>
            <?php else: ?>
                <?php foreach ($recipes as $index => $recipe): ?>
                    <?php 
                        // Pick a badge color based on category
                        $badgeClass = "bg-primary-subtle text-primary";
                        if ($recipe['category'] == 'Breakfast') $badgeClass = "bg-success-subtle text-success";
                        if ($recipe['category'] == 'Dessert') $badgeClass = "bg-warning-subtle text-warning";
                        
                        $imgUrl = !empty($recipe['image_url']) ? htmlspecialchars($recipe['image_url']) : "https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&w=800&q=80";
                    ?>
                    <div class="col-md-6 col-lg-4 reveal">
                        <div class="card recipe-card shadow-sm h-100">
                            <img src="<?php echo $imgUrl; ?>" class="card-img-top" alt="<?php echo htmlspecialchars($recipe['title']); ?>">
                            <div class="card-body">
                                <span class="badge <?php echo $badgeClass; ?> mb-2"><?php echo htmlspecialchars($recipe['category']); ?></span>
                                <h4 class="card-title fw-bold"><?php echo htmlspecialchars($recipe['title']); ?></h4>
                                <p class="text-muted small">Flavorful <?php echo htmlspecialchars($recipe['category']); ?> prepared in <?php echo (int)$recipe['prep_time']; ?> minutes.</p>
                                <hr class="my-3 opacity-10">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-muted small">⏱ <?php echo (int)$recipe['prep_time']; ?> mins</span>
                                    <a href="javascript:void(0)" onclick="openRecipeDetails(<?php echo $index; ?>)"
                                        class="btn btn-link text-success p-0 text-decoration-none fw-bold">View Details →</a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </section>
      <div class="modal fade" id="recipeModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 rounded-4 shadow-lg">
                <div class="modal-header border-0 pb-0">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 p-md-5 pt-0">
                    <div class="row g-4">
                        <div class="col-md-5">
                            <img id="modalImg" src="" class="img-fluid rounded-4 shadow-sm" alt="Recipe">
                        </div>
                        <div class="col-md-7">
                            <span id="modalCategory" class="badge bg-success-subtle text-success mb-2">Category</span>
                            <h2 id="modalTitle" class="display-6 fw-bold mb-3">Recipe Title</h2>
                            <p id="modalDesc" class="text-muted mb-4">Description of the recipe goes here...</p>

                            <h5 class="fw-bold mb-3">Ingredients</h5>
                            <ul id="modalIngredients" class="list-unstyled ingredients-list mb-4">
                           
                            </ul>
                        </div>
                    </div>
                    <div class="mt-4 pt-4 border-top">
                        <h5 class="fw-bold mb-3">Preparation Steps</h5>
                        <p id="modalInstructions" class="text-muted" style="line-height: 1.8;">Steps go here...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
 <footer class="bg-white border-top py-5" id="contact">
        <div class="container text-center">
            <div class="mb-3">
                <a href="index.php" class="text-decoration-none text-muted mx-2 small">Home</a>
                <a href="recipes.php" class="text-decoration-none text-muted mx-2 small">Recipes</a>
                <a href="submit-recipe.php" class="text-decoration-none text-muted mx-2 small">Submit</a>
                <a href="about.php" class="text-decoration-none text-muted mx-2 small">About</a>
            </div>
            <p class="text-muted small mb-0">© 2026 RecipeVault. Powered by Culinary Passion.</p>
        </div>
    </footer>
     <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/script.js"></script>
</body>
</html>
