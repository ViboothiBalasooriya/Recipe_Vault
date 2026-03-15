# 🍴 Digital Recipe Vault | Interactive Web Application

An interactive web application for culinary enthusiasts to discover, search, and submit recipes. Built as a mini-project for the COM 2303 - Web Design course at Rajarata University of Sri Lanka.

## 🚀 Key Features

- **Dynamic Recipe Search**: Real-time filtering of recipes by title or category on the "Recipes" page.
- **Dynamic Content Loading**: Centralized data system populating recipe details into interactive modals.
- **Recipe Submission**: A dedicated interface for user-contributed recipes with robust JavaScript validation.
- **Interactive Carousel**: Premium homepage carousel highlighting featured recipes using Bootstrap.
- **Premium UX**: Glassmorphism header, smooth scroll navigation, and a fully responsive layout.

## 🛠 Technologies Used

- **HTML5**: Semantic page structure.
- **CSS3**: Custom premium styling with Glassmorphism and HSL color systems.
- **Bootstrap 5**: Grid system, Carousel, and Layout utilities.
- **JavaScript (ES6)**: Dynamic search, form validation, and DOM manipulation.
- **Font Awesome**: Modern iconography.

## 📁 Project Structure

```text
Recipe_Vault/
├── index.html           # Home Page with Hero & Featured sections
├── recipes.html         # Recipe Grid with Search & Modals
├── submit-recipe.html   # Recipe Submission Form
├── about.html           # About page & Academic Credits
├── login.html           # Authentication portal
├── script.js            # Core application logic & Interactivity
├── style.css            # Custom styling & Design tokens
└── proposal.txt         # Original project proposal
```

## 💻 How to Run

1. Clone or download the repository.
2. Open `index.html` in any modern web browser.
3. No server-side setup or database is required as the application uses client-side logic.

*Created with passion for food and code.*

## 🚨 Note for Downloaded Copies (CSS/JS Paths)

If CSS or JavaScript does not load after downloading, it is usually due to asset paths in HTML files:

- `/Recipe_Vault/css/style.css`
- `/Recipe_Vault/js/script.js`

These absolute paths can fail when opening files directly with `file://`.

### Recommended

Run the project with a local server from the project root:

```bash
python -m http.server 5500
```

Then open:

```text
http://localhost:5500/index.html
```

You can also use VS Code Live Server.

## 🎓 Academic Credits

**Course**: COM 2303 - Web Design  
**University**: Rajarata University of Sri Lanka  

---

