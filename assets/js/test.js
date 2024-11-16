class RecipeCard {
    constructor(imageFile, description, rating) {
        this.imageFile = imageFile;
        this.description = description;
        this.rating = rating;
    }

    createCard() {
        const card = document.createElement('div');
        card.className = 'card';

        const img = document.createElement('img');
        img.src = URL.createObjectURL(this.imageFile);
        card.appendChild(img);

        const title = document.createElement('h3');
        title.textContent = this.description;
        card.appendChild(title);

        const ratingText = document.createElement('p');
        ratingText.textContent = `Rating: ${this.rating}/5`;
        card.appendChild(ratingText);

        // Add click event to the card
        card.addEventListener('click', () => this.showDetails());

        return card;
    }

    showDetails() {
        const modal = document.getElementById('modal');
        const modalDetails = document.getElementById('modalDetails');

        modalDetails.textContent = `
            Recipe Description: ${this.description}
            Rating: ${this.rating}/5
        `;

        modal.style.display = "block"; // Show the modal

        // Close the modal when the 'x' is clicked
        document.getElementById('closeModal').onclick = () => {
            modal.style.display = "none";
        };

        // Close the modal when clicking outside of it
        window.onclick = (event) => {
            if (event.target === modal) {
                modal.style.display = "none";
            }
        };
    }
}

document.getElementById('createCardButton').addEventListener('click', function() {
    const imageInput = document.getElementById('imageInput');
    const descriptionInput = document.getElementById('descriptionInput');
    const ratingInput = document.getElementById('ratingInput');

    const imageFile = imageInput.files[0];
    const description = descriptionInput.value;
    const rating = ratingInput.value;

    if (imageFile && description && rating) {
        const cardContainer = document.getElementById('cardsContainer');

        // Create a new RecipeCard instance
        const recipeCard = new RecipeCard(imageFile, description, rating);
        const cardElement = recipeCard.createCard();

        // Append the created card to the container
        cardContainer.appendChild(cardElement);

        // Clear inputs
        imageInput.value = '';
        descriptionInput.value = '';
        ratingInput.value = '';
    } else {
        alert("Please fill all fields.");
    }
});
