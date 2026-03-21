# 🍳 RecipeBook (formerly RecipeVault)

RecipeBook is a premium, full-stack PHP web application that allows culinary enthusiasts to discover, craft, and share their favorite recipes with the world. Built with a modern, responsive aesthetic, it seamlessly blends high-end UI design with robust backend PHP and MySQL integration.

## ✨ Key Features

- **Modern UI/UX Analytics**: Stunning, pixel-perfect interfaces built with Bootstrap 5. Includes a gorgeous dual-column split-screen layout for authentication pages and a dynamic hero carousel.
- **Full Authentication System**: Secure user registration and login flows using industry-standard BCRYPT password hashing and PHP Session handling.
- **Dynamic Recipe Engine**: Logged-in users can securely upload recipes with custom image attachments.
- **Dynamic Dashboard**: Personalized user profiles feature live database statistics and beautifully rendered, database-driven recipe grids complete with custom status badges.
- **Auto-Seeding Database**: Running locally? The app intelligently detects if your database is empty. The moment you navigate to the Recipes view, the PHP backend will natively auto-inject 6 beautiful, baseline recipes directly into your database to get you started instantly—no CLI or SQL manual execution required!

## 🚀 Tech Stack

- **Frontend**: HTML5, CSS3, JavaScript, Bootstrap 5.3, Font Awesome 6
- **Backend**: Native PHP 8+
- **Database**: MySQL (using `mysqli` procedural bindings)
- **Assets**: Dynamic imagery sourced via Unsplash

## 🔧 Installation & Setup

1. **Prerequisites**: Ensure you have a local web server (like XAMPP, Laragon, or MAMP) installed with PHP and MySQL active.
2. **Clone/Move the Repository**: Place this project folder (`Recipe_Vault`) into your web server's public directory (e.g., `C:\xampp\htdocs\` for XAMPP).
3. **Database Configuration**:
   - Open your MySQL management tool (e.g., phpMyAdmin).
   - Create a new empty database named `recipe_vault`.
   - Import the provided `database.sql` file to structure the `users` and `recipes` tables.
4. **Launch**:
   - Start Apache and MySQL from your local server dashboard.
   - Navigate to `http://localhost/Recipe_Vault/index.php` in your browser.
   - Click **Recipes** in the navigation bar to trigger the auto-seed feature and instantly populate your platform with content!

## 📂 Project Structure

- `\auth`: Contains `login.php` and `register.php` handling secure session state.
- `\includes`: houses `db.php` (your MySQL connector) and `functions.php` (global helper functions).
- `\images\uploads`: Destination directory for user-uploaded recipe images.
- `\css` & `\js`: Custom frontend asset pipelines.
- `dashboard.php`, `submit-recipe.php`, `recipes.php`, etc. (Core functional pages).

## 💡 Usage Highlights
- **Submit Recipe**: Users can attach `.jpg`, `.png`, or `.webp` files. The system securely verifies headers and moves uploads natively behind the scenes.
- **My Profile**: Visit the dashboard at any time to review the recipes you have personally published to the platform!

---
*Built with passion for culinary excellence and clean code.*
