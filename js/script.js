const recipesData = [
    {
        title: "Classic Honey Pancakes",
        category: "Breakfast",
        desc: "Fluffy, golden pancakes served with organic honey and fresh berries.",
        ingredients: ["2 cups Flour", "1 cup Milk", "2 Eggs", "3 tbsp Honey"],
        steps: "1. Mix dry ingredients. 2. Whisk wet ingredients. 3. Cook on griddle until bubbles form.",
        img: "https://images.unsplash.com/photo-1567620905732-2d1ec7ab7445"
    },
    {
        title: "Signature Harvest Bowl",
        category: "Vegetarian",
        desc: "A vibrant blend of seasonal greens and zesty tahini dressing.",
        ingredients: ["Kale", "Quinoa", "Sweet Potato", "Tahini"],
        steps: "1. Roast potatoes. 2. Massage kale. 3. Combine and drizzle dressing.",
        img: "https://images.unsplash.com/photo-1546069901-ba9599a7e63c"
    },
    {
        title: "Dark Chocolate Lava",
        category: "Dessert",
        desc: "Molten dark chocolate center with a hint of sea salt and vanilla bean.",
        ingredients: ["Dark Chocolate", "Butter", "Sugar", "Vanilla"],
        steps: "1. Melt chocolate. 2. Fold in egg mixture. 3. Bake for 12 mins until firm edges.",
        img: "https://images.unsplash.com/photo-1624353365286-3f8d62daad51"
    },
    {
        title: "Creamy Mushroom Risotto",
        category: "Dinner",
        desc: "Slow-cooked Arborio rice with wild mushrooms and truffle oil.",
        ingredients: ["Arborio Rice", "Mushrooms", "Parmesan", "Truffle Oil"],
        steps: "1. Sauté mushrooms. 2. Gradually add broth to rice. 3. Stir in cheese and oil.",
        img: "https://images.unsplash.com/photo-1476124369491-e7addf5db371"
    },
    {
        title: "Spicy Thai Green Curry",
        category: "Dinner",
        desc: "A fragrant blend of green chillies, lemongrass, and coconut milk.",
        ingredients: ["Thai Green Curry Paste", "Coconut Milk", "Chicken/Tofu", "Bamboo Shoots"],
        steps: "1. Fry curry paste. 2. Add coconut milk and protein. 3. Simmer with bamboo shoots.",
        img: "https://images.unsplash.com/photo-1455619452474-d2be8b1e70cd"
    },
    {
        title: "Summer Berry Tart",
        category: "Dessert",
        desc: "Crispy pastry shell filled with vanilla custard and seasonal berries.",
        ingredients: ["Pastry Crust", "Vanilla Custard", "Mixed Berries", "Apricot Glaze"],
        steps: "1. Bake crust. 2. Fill with custard. 3. Decorate with berries and glaze.",
        img: "https://images.unsplash.com/photo-1519915028121-7d3463d20b13"
    }
];
function openRecipeDetails(index) {
    const recipe = recipesData[index];
    if (!recipe) return;

    document.getElementById("modalTitle").innerText = recipe.title;
    document.getElementById("modalImg").src = recipe.img + "?auto=format&fit=crop&w=800&q=80";
    document.getElementById("modalCategory").innerText = recipe.category;
    document.getElementById("modalDesc").innerText = recipe.desc;

    // Populate Ingredients
    const ingList = document.getElementById("modalIngredients");
    ingList.innerHTML = recipe.ingredients.map(ing => `<li><i class="fas fa-check text-success me-2"></i> ${ing}</li>`).join("");

    // Populate Instructions
    document.getElementById("modalInstructions").innerText = recipe.steps;

    // Show Modal using Bootstrap instance
    const modalElement = document.getElementById('recipeModal');
    const bModal = new bootstrap.Modal(modalElement);
    bModal.show();
}
document.addEventListener("DOMContentLoaded", function () {
    // Reveal Observer
    const revealObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('active');
            }
        });
    }, { threshold: 0.1 });

    document.querySelectorAll('.reveal').forEach(el => revealObserver.observe(el));

    //  Dynamic Search & Filter Logic
    const searchInput = document.getElementById("searchInput");
    const categoryFilter = document.getElementById("categoryFilter");

    if (searchInput || categoryFilter) {
        const filterFn = () => {
            const query = searchInput.value.toLowerCase();
            const category = categoryFilter.value;

            document.querySelectorAll(".reveal").forEach(block => {
                const title = block.querySelector(".card-title")?.innerText.toLowerCase();
                const badge = block.querySelector(".badge")?.innerText;

                if (!title) return;

                const matchesSearch = title.includes(query);
                const matchesCategory = category === "All" || badge === category;

                block.style.display = (matchesSearch && matchesCategory) ? "block" : "none";
            });
        };

        searchInput?.addEventListener("input", filterFn);
        categoryFilter?.addEventListener("change", filterFn);
    }

    //  Form Validation (Feature 3)
    const submitForm = document.getElementById("submitRecipeForm");
    if (submitForm) {
        submitForm.addEventListener("submit", function (e) {
            e.preventDefault();

            // Get values
            const name = document.getElementById("chefName").value.trim();
            const email = document.getElementById("chefEmail").value.trim();
            const title = document.getElementById("recipeTitle").value.trim();

            // 1. Empty Input Prevention (JS)
            if (name === "" || email === "" || title === "") {
                alert("⚠️ Please fill in all required fields (Name, Email, and Recipe Title).");
                return;
            }

            // 2. Correct Email Format (JS)
            if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                alert("⚠️ Please enter a valid email address!");
                return;
            }

            // Success
            alert("🎉 Recipe submitted successfully! Your culinary masterpiece will be reviewed shortly. 👨‍🍳");
            submitForm.reset();
        });
    }

    //  Smooth Scroll
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            target?.scrollIntoView({ behavior: 'smooth' });
        });
    });
});// ...existing code...

document.addEventListener("DOMContentLoaded", function () {
    // Reveal Observer
    const revealObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('active');
            }
        });
    }, { threshold: 0.1 });

    document.querySelectorAll('.reveal').forEach(el => revealObserver.observe(el));

    // Dynamic Search & Filter Logic (FIXED)
    const searchInput = document.getElementById("searchInput");
    const categoryFilter = document.getElementById("categoryFilter");

    
    if (searchInput && categoryFilter) {
        const filterFn = () => {
            const query = searchInput.value.toLowerCase();
            const category = categoryFilter.value;

            // Target recipe cards directly
            document.querySelectorAll(".recipe-card").forEach(card => {
                const title = card.querySelector(".card-title")?.innerText.toLowerCase() || "";
                const desc = card.querySelector(".text-muted.small")?.innerText.toLowerCase() || "";
                const badge = card.querySelector(".badge")?.innerText || "";

                const matchesSearch = title.includes(query) || desc.includes(query);
                const matchesCategory = category === "All" || badge.includes(category);

                // Show/hide the col-md-4 container
                const cardContainer = card.closest(".col-md-4");
                if (cardContainer) {
                    cardContainer.style.display = (matchesSearch && matchesCategory) ? "block" : "none";
                }
            });
        };

        searchInput.addEventListener("input", filterFn);
        categoryFilter.addEventListener("change", filterFn);
    }

    
});
function reveal() {
  const reveals = document.querySelectorAll(".reveal");

  reveals.forEach((element) => {
    const windowHeight = window.innerHeight;
    const elementTop = element.getBoundingClientRect().top;
    const elementVisible = 100;

    if (elementTop < windowHeight - elementVisible) {
      element.classList.add("active");
    }
  });
}

window.addEventListener("scroll", reveal);
window.addEventListener("load", reveal);