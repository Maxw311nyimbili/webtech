function viewRecipe(recipeId) {
    fetch(`../actions/view_recipe.php?id=${recipeId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const recipe = data.recipe; // This is an array of recipes and ingredients
                let recipeHtml = `
                    <p><img src="${recipe[0].image_url}" alt="${recipe[0].food_name}" width="400px" /></p>
                    <p><strong>Food Name:</strong> ${recipe[0].food_name}</p>
                    <p><strong>Origin:</strong> ${recipe[0].origin}</p>
                    <p><strong>Type:</strong> ${recipe[0].type}</p>
                    <p><strong>Instructions:</strong> ${recipe[0].instructions}</p>
                    <p><strong>Description:</strong> ${recipe[0].description}</p>
                    <p><strong>Preparation Time:</strong> ${recipe[0].preparation_time} mins</p>
                    <p><strong>Cooking Time:</strong> ${recipe[0].cooking_time} mins</p>
                    <p><strong>Serving Size:</strong> ${recipe[0].serving_size}</p>
                    <p><strong>Calories per Serving:</strong> ${recipe[0].calories_per_serving}</p>
                    <h4>Ingredients:</h4>
                    <ul>`;
                
                // Loop through ingredients and display them
                recipe.forEach(item => {
                    recipeHtml += `
                        <li>${item.ingredient_name} - ${item.quantity} ${item.unit}</li>
                    `;
                });

                recipeHtml += `</ul>`;
                document.getElementById('modalRecipeDetails').innerHTML = recipeHtml;
                document.getElementById('recipeModal').style.display = 'block';
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => console.error('Error:', error));
}

function closeRecipeModal() {
    document.getElementById('recipeModal').style.display = 'none';
}


// EDIT FUNCTION
function editRecipe(recipeId) {
    fetch(`../actions/edit_recipe_GET.php?id=${recipeId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const recipe = data.recipe[0]; // Assuming only one recipe is returned
                document.getElementById('editRecipeId').value = recipe.recipe_id;
                document.getElementById('editFoodId').value = recipe.food_id;
                document.getElementById('editIngredientId').value = recipe.ingredient_id;
                document.getElementById('editQuantity').value = recipe.quantity;
                document.getElementById('editUnit').value = recipe.unit;
                document.getElementById('editOptional').checked = recipe.optional === 1;

                document.getElementById('editRecipeModal').style.display = 'block';
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => console.error('Error:', error));
}

function updateRecipe() {
    const form = document.getElementById('editRecipeForm');
    const formData = new FormData(form);

    fetch('../actions/edit_recipe_POST.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Recipe updated successfully');
            closeEditRecipeModal();
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => console.error('Error:', error));
}

function closeEditRecipeModal() {
    document.getElementById('editRecipeModal').style.display = 'none';
}



// DELETE FUNCTION
function deleteRecipe(foodId) {
    if(confirm("Are you sure you want to delete this recipe?")){
        fetch(`../actions/delete_recipe.php`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: `id=${foodId}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                location.reload();
            } else {
                alert(data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }
}

